(function ($) {
  var datasets = [];

  function initialize_field($field) {
    const dataset = $field && $field[0].dataset;
    if (!dataset) return false;
    datasets.push(dataset);
  }

  $(document).ready(function () {
    $(".acf-galerie-4-attachments").sortable({
      placeholder: "ui-state-highlight",
    });
    $(".acf-galerie-4-attachments").disableSelection();
  });

  function wp_media_library(container, dataset) {
    var mimeTypes = container.data("mime-types") || "";
    var buttonLabel = container.data("button-label") || "Add Media";
    var libraryOptions = {};

    if (mimeTypes.trim() !== "") {
      var types = mimeTypes.split(",").map(function(ext) {
        return ext.trim().toLowerCase();
      }).filter(function(ext) {
        return ext !== "";
      });
      if (types.length > 0) {
        libraryOptions.type = types;
      }
    }

    if (typeof wp.media !== "undefined") {
      const frame = wp.media({
        title: buttonLabel,
        button: { text: buttonLabel },
        multiple: "add",
        library: libraryOptions
      });

      frame.on("select", function () {
        var attachments = frame.state().get("selection").toJSON();
        if (!attachments) return;

        var maxSelection = parseInt(container.data("max-selection")) || 0;
        if (maxSelection > 0 && attachments.length > maxSelection) {
          attachments = attachments.slice(0, maxSelection);
          alert("Maximum selection of " + maxSelection + " item" + (maxSelection !== 1 ? "s" : "") + " allowed. Only the first " + maxSelection + " item" + (maxSelection !== 1 ? "s were" : " was") + " added.");
        }

        render_attachments(attachments, container, dataset);
        validate_selection(container);
      });

      //https://stackoverflow.com/a/13963342/3667332
      frame.on("open", function () {
        var selection = frame.state().get("selection");
        var ids = [];

        $(container)
          .children()
          .each(function () {
            var dataId = $(this).data("id");
            ids.push(Number(dataId));
          });

        if (ids.length > 0) {
          ids.forEach(function (id) {
            attachment = wp.media.attachment(id);
            selection.add(attachment ? [attachment] : []);
          });
        }
      });

      frame.open();
    } else {
      throw "wp.media is not available";
    }
  }

  $(document).on("click", ".acf-galerie-4-add-media", function (e) {
    const container = $(this)
      .closest(".acf-galerie-4-container")
      .find(".acf-galerie-4-attachments");

    const field_name = $(this)
      .closest(".acf-galerie-4-container")
      .find('input[type="hidden"]')[0].name;

    const key = container
      .attr("class")
      .match(/acf-galerie-4-attachments-(\w+)/)[1];

    const dataset = datasets.find((x) => x.key === key);
    dataset["field_name"] = field_name;

    wp_media_library(container, dataset);
  });

  function render_attachments(attachments, container, dataset) {
    var $html = "";

    attachments.forEach(function (attachment) {
      var icon = attachment.icon;
      var thumbnail_class = "acf-galerie-4-attachment-icon";
      var title = attachment.title || attachment.filename || "";
      var titleEscaped = $("<div/>").text(title).html();
      var titleAttr = titleEscaped.replace(/"/g, "&quot;");

      if (attachment && attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) {
        icon = attachment.sizes.medium.url;
        thumbnail_class = "acf-galerie-4-attachment-thumbnail";
      } else if (attachment && attachment.sizes && attachment.sizes.full && attachment.sizes.full.url) {
        icon = attachment.sizes.full.url;
        thumbnail_class = "acf-galerie-4-attachment-thumbnail";
      } else if (attachment.url && attachment.type === "image") {
        icon = attachment.url;
        thumbnail_class = "acf-galerie-4-attachment-thumbnail";
      }

      $html += '<div data-id="' + attachment.id + '" class="attachment-thumbnail-container attachment-thumbnail-container-' + attachment.id + ' ' + thumbnail_class + '">';
      $html += '  <input type="hidden" name="' + dataset.field_name + '[]" value="' + attachment.id + '" />';
      $html += '  <button type="button" class="acf-galerie-4-remove-attachment" title="Remove this media">';
      $html += '    <span class="dashicons dashicons-trash"></span>';
      $html += '  </button>';
      $html += '  <img src="' + icon + '" alt="' + titleAttr + '" title="' + titleAttr + '" />';
      
      if (thumbnail_class === "acf-galerie-4-attachment-icon") {
        $html += '  <div class="acf-galerie-4-file-name">' + titleAttr + '</div>';
      }
      
      $html += '</div>';
    });

    $(container).html($html);
  }

  function validate_selection(container) {
    var $container = $(container).closest(".acf-galerie-4-container");
    var $notice = $container.find(".acf-galerie-4-validation-notice");
    var count = $(container).children().length;
    var min = parseInt($(container).data("min-selection")) || 0;
    var max = parseInt($(container).data("max-selection")) || 0;
    var message = "";

    if (min > 0 && count < min) {
      message = "This gallery requires a minimum of " + min + " item" + (min !== 1 ? "s" : "") + ". Currently selected: " + count + ".";
    } else if (max > 0 && count > max) {
      message = "This gallery allows a maximum of " + max + " item" + (max !== 1 ? "s" : "") + ". Currently selected: " + count + ".";
    }

    if (message) {
      $notice.text(message).show();
    } else {
      $notice.text("").hide();
    }
  }

  $(document).on(
    "click touchend",
    ".acf-field-galerie-4 .acf-galerie-4-remove-attachment",
    function () {
      var id = $(this).closest(".attachment-thumbnail-container").data("id");

      if (
        id &&
        confirm(
          "You are about to remove this media from the gallery, are you sure?"
        )
      ) {
        var $container = $(`.attachment-thumbnail-container-${id}`).closest(".acf-galerie-4-attachments");
        $(`.attachment-thumbnail-container-${id}`).remove();
        validate_selection($container);
      }
    }
  );

  $(document).on(
    "touchstart",
    ".acf-field-galerie-4 .attachment-thumbnail-container img",
    function (event) {
      event.stopPropagation();

      let $button = $(this)
        .closest(".attachment-thumbnail-container")
        .find(".acf-galerie-4-remove-attachment");

      if ($button.css("opacity") === "1") {
        $button.css("opacity", "0");
        $button.css("display", "none");
      } else {
        $(".acf-galerie-4-remove-attachment").css("opacity", "0");
        $button.css("opacity", "1");
        $button.css("display", "inline-block");
      }
    }
  );

  if (typeof acf.add_action !== "undefined") {
    /**
     * Run initialize_field when existing fields of this type load,
     * or when new fields are appended via repeaters or similar.
     */
    acf.add_action("ready_field/type=galerie-4", initialize_field);
    acf.add_action("append_field/type=galerie-4", initialize_field);
  }
})(jQuery);
