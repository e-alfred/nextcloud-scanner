/**
 * Nextcloud - scanner
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Greg Sutcliffe <nextcloud@emeraldreverie.org>
 * @copyright Greg Sutcliffe 2016
 */

(function ($, OC) {

  $(document).ready(function() {

    $('#scan').click(function () {
      var url = OC.generateUrl('/apps/scanner/scan');

      $.get(url).success(function (response) {
        var path = OC.imagePath('scanner', response.image);
        $("#scan-image").attr("src",path);
        $("#scan-image").removeClass('hidden');
      });

    });
  });

})(jQuery, OC);
