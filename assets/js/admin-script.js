(function ($) {
  $(document).on("click", "a#acfg4-migrate", function (e) {
    e.preventDefault();

    if ($("#acfg4-migrate-popup").length === 0) {
      const popupHtml = `
          <div id="acfg4-migrate-popup-overlay"></div>
          <div id="acfg4-migrate-popup">
            <h2>ACF Galerie 4 — Migration</h2>
            <div class="notice"></div>
            <p>This tool will assist you to migrate from ACF Photo Gallery Field or ACF Gallery Pro to ACF Galerie 4.</p>
            <p>
                <label class="migrate-helper-text">Choose plugin you want to migrate from?</label>
                <select name="migrate-from">
                    <option value="1">ACF Photo Gallery Field</option>
                    <option value="2">ACF Gallery Pro</option>
                </select>
                <span class="spacer-between-from-two">to</span>
                <input type="text" value="ACF Galerie 4" readonly />
            </p>
            <p>Important Notes:</p>
            <ul class="important-note-list">
              <li>I have backed up my website.</li>
              <li>I understand that some features may not work as expected.</li>
              <li>I acknowledge that ACF Galerie 4 is not responsible for any damage to my website.</li>
              <li>I have the necessary knowledge to restore my website if something goes wrong.</li>
              <li>I understand that the WordPress REST API will not be migrated.</li>
              <li>I understand that galleries created with Elementor will not be migrated.</li>
              <li>I understand that I may need to modify the source code of my website.</li>
            </ul>
            <p>
                <label>
                    <input type="checkbox" name="agree"/> I have read the important notes.
                </label>
            </p>
            <div class="action-buttons">
                <button class="button" type="cancel" title="Cancel">Cancel</button>
                <button class="button button-primary" type="submit" title="Start Migration" disabled>Start Migration</button>
            </div>
          </div>
        `;

      $("body").append(popupHtml);
    }

    $("#acfg4-migrate-popup, #acfg4-migrate-popup-overlay").fadeIn();
  });

  $(document).on(
    "change",
    "#acfg4-migrate-popup input[name='agree']",
    function () {
      const submitButton = $(".action-buttons .button-primary");
      submitButton.prop("disabled", !this.checked);
    }
  );

  $(document).on(
    "click",
    "#acfg4-migrate-popup .action-buttons button[type='cancel']",
    function () {
      $("#acfg4-migrate-popup, #acfg4-migrate-popup-overlay").fadeOut();
    }
  );

  $(document).on(
    "click",
    "#acfg4-migrate-popup .action-buttons button[type='submit']",
    function () {
      const action = $("#acfg4-migrate-popup .action-buttons");
      const notice = $("#acfg4-migrate-popup .notice");
      action.fadeOut();
      notice.removeClass("notice-success notice-error").text("");
      notice
        .addClass("notice-success")
        .text("Migration has started. Please wait...")
        .fadeIn();

      $.ajax({
        url: ajaxurl,
        method: "POST",
        data: {
          action: "acfg4_start_migration",
          migrate_from: $(
            "#acfg4-migrate-popup select[name='migrate-from']"
          ).val(),
          nonce: acfg4_start_migration_nonce,
        },
        success: function (data) {
          action.fadeIn();
          notice
            .removeClass("notice-success notice-error")
            .addClass("notice-success")
            .text(data.data.message)
            .fadeIn();
        },
        error: function (xhr, status, error) {
          action.fadeIn();
          let errorMessage = "An error occurred.";

          try {
            const response = JSON.parse(xhr.responseText);
            if (response && response.data && response.data.message) {
              errorMessage = response.data.message;
            }
          } catch (e) {
            throw e;
          }

          notice
            .removeClass("notice-success notice-error")
            .addClass("notice-error")
            .text(errorMessage)
            .fadeIn();
        },
      });
    }
  );

  $(document).ready(function () {
    if (
      typeof acf_gallery_4_pro_localize !== "undefined" &&
      acf_gallery_4_pro_localize?.license_activate === "1"
    ) {
      return;
    }

    $(
      "body.acf-admin-page #wpcontent .acf-admin-toolbar .acf-nav-upgrade-wrap"
    ).prepend(
      `<a target="_blank" href="https://galerie4.com/" class="btn-upgrade acf-admin-toolbar-upgrade-btn" style="margin-right:15px;">
        <p>Upgrade to ACF Galerie 4 Pro</p>
      </a>`
    );
  });
})(jQuery);
