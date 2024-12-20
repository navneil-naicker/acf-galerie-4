(function ($) {
  $(document).on("click", "a#acfg4-migrate", function (e) {
    e.preventDefault();

    if ($("#acfg4-popup").length === 0) {
      const popupHtml = `
          <div id="acfg4-popup-overlay"></div>
          <div id="acfg4-popup">
            <h2>ACF Galerie 4 — Migration</h2>
            <p>This tool will assist you to migrate from ACF Photo Gallery Field or ACF Gallery Pro to ACF Galerie 4.</p>
            <p>
                <label>Choose plugin you want to migrate from?</label>
                <select>
                    <option>ACF Photo Gallery Field</option>
                    <option>ACF Gallery Pro</option>
                </select>
                <span>to</span>
                <input value="ACF Galerie 4" readonly />
            </p>
            <p>Important Note:</p>
            <ul>
                <li>I have taken backup of my website.</li>
                <li>I understand that not all the features will work as expected.</li>
                <li>I understand that ACF Galerie 4 takes no responsbility for any damage done to my website.</li>
                <li>I have relevant knowledge to revert the website incase something goes wrong.</li>
                <li>I understand that WordPress REST API will not be migrated.</li>
                <li>I understand that galleries built using Elementor will not be migrated.</li>
                <li>I understand that I will have to make changes in the source code of my website.</li>
            </ul>
            <p>
                <label>
                    <input type="checkbox"/> I have read the important notice.
                </label>
            </p>
            <div class="acfg4-action-buttons">
                <button id="acfg4-close-popup">Close</button>
                <button id="acfg4-start-migration">Start Migration</button>
            </div>
          </div>
        `;

      $("body").append(popupHtml);
    }

    $("#acfg4-popup, #acfg4-popup-overlay").fadeIn();
  });

  $(document).on(
    "click",
    "#acfg4-close-popup, #acfg4-popup-overlay",
    function () {
      $("#acfg4-popup, #acfg4-popup-overlay").fadeOut(function () {
        $(this).remove();
      });
    }
  );

  $(document).on("click", "button#acfg4-start-migration", function () {
    $.ajax({
      url: ajaxurl,
      method: "GET",
      action: "my_logged_in_user_ajax_function",
      success: function (data) {
        console.log(data);
      },
      error: function (xhr, status, error) {
        console.log(error);
      },
    });
  });
})(jQuery);
