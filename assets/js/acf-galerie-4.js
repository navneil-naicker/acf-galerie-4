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
    if (typeof wp.media !== "undefined") {
      const frame = wp.media({
        title: "Add Media",
        button: { text: "Add Media" },
        multiple: "add",
      });

      frame.on("select", function () {
        const attachments = frame.state().get("selection").toJSON();
        if (!attachments) return;

        render_attachments(attachments, container, dataset);
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
    var container = $(this)
      .closest(".acf-galerie-4-container")
      .find(".acf-galerie-4-attachments");

    var key = container
      .attr("class")
      .match(/acf-galerie-4-attachments-(\w+)/)[1];

    var dataset = datasets.find((x) => x.key === key);

    wp_media_library(container, dataset);
  });

  function render_attachments(attachments, container, dataset) {
    $html = "";

    attachments.forEach((attachment) => {
      icon = attachment.icon;
      thumbnail_class = "acf-galerie-4-attachment-icon";

      if (attachment?.sizes?.medium?.url) {
        icon = attachment.sizes.medium.url;
        thumbnail_class = "acf-galerie-4-attachment-thumbnail";
      }

      $html += `<div data-id="${attachment.id}" class="${thumbnail_class}">`;
      $html += `<img src='${icon}'/>`;
      $html += `<input type="hidden" value="${attachment.id}" name="${dataset.type}[${dataset.name}][]"/>`;
      $html += `</div>`;
    });

    $(container).html($html);
  }

  $(document).on(
    "click",
    ".acf-field-galerie-4 .acf-galerie-4-remove-attachment ",
    function () {
      var id = $(this).closest(".attachment-thumbnail-container").data("id");

      if (
        id &&
        confirm(
          "You are about to remove this media from the gallery, are you sure?"
        )
      ) {
        $(`.attachment-thumbnail-container-${id}`).remove();
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
