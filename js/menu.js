/**
 * Nextcloud - scanner
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Greg Sutcliffe <nextcloud@emeraldreverie.org>
 * @copyright Greg Sutcliffe 2016
 */

var ScannerMenuPlugin = {
  attach: function (menu) {
    var fileList = menu.fileList;

    menu.addMenuEntry({
      id: 'scanner',
      displayName: 'Scan Image',
      templateName: 'scan.jpg',
      iconClass: 'icon-filetype-scanner',
      fileType: 'file',
      actionHandler: function(name) {

        var dir = fileList.getCurrentDirectory();
        // TODO: Should this be ajax?
        // TODO: Scans are slow, show a spinner / bar
        $.ajax({
          url: OC.generateUrl('/apps/scanner/scan'),
          async: false,
          type: 'POST',
          data: {
            filename: name,
            dir: dir
          },
          success: function(data) {
            console.log(data);
          }
        });
      }
    });
  }
};
OC.Plugins.register('OCA.Files.NewFileMenu', ScannerMenuPlugin);
