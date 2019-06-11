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
		var plugin = this;
		var fileList = menu.fileList;

		menu.addMenuEntry({
			id: 'scanner',
			displayName: t('scanner', 'Scan Image'),
			templateName: 'scan.jpg',
			iconClass: 'icon-filetype-scanner',
			fileType: 'file',
			actionHandler: function (name) {
				plugin.scanOptionsModal(t('scanner', 'Mode'), t('scanner', 'Color'), t('scanner', 'Greyscale'), t('scanner', 'Lineart'), t('scanner', 'Resolution'), t('scanner', 'Please adjust scan parameters'), t('scanner', 'Scan Options'), function (result, formData) {
					if (!result) {
						OC.Notification.showTemporary(t('scanner', 'Scan aborted.'));
						return;
					}
					var dir = fileList.getCurrentDirectory();
					OC.Notification.showTemporary(t('scanner', 'Scan started.'));
					$.ajax({
						url: OC.generateUrl('/apps/scanner/scan'),
						async: true,
						type: 'POST',
						data: {
							filename: name,
							dir: dir,
							scanOptions: formData
						},
						success: function (data) {
							fileList.changeDirectory(dir, true, true);
							OC.Notification.showTemporary(t('scanner', 'Scan complete'));
						},
						error: function (data) {
							OC.Notification.showTemporary(data.result);
						}
					});
				});

			}
		});
	},

	getTemplate: function (tplFile) {
		var defer = $.Deferred();
		var self = this;
		$.get(OC.filePath('scanner', 'templates', tplFile), function (tmpl) {
			self.$messageTemplate = $(tmpl);
			defer.resolve(self.$messageTemplate);
		})
			.fail(function (jqXHR, textStatus, errorThrown) {
				defer.reject(jqXHR.status, errorThrown);
			});
		return defer.promise();
	},

	scanOptionsModal: function (mode, color, greyscale, lineart, resolution, text, title, callback, modal) {
		var plugin = this;
		return $.when(this.getTemplate('optionsdialog.html')).then(function ($tmpl) {
			var dialogName = 'oc-dialog-' + OCdialogs.dialogsCounter + '-content';
			var dialogId = '#' + dialogName;
			var $dlg = $tmpl.octemplate({
				dialog_name: dialogName,
				title: title,
				message: text,
				resolution: resolution,
				mode: mode,
				color: color,
				greyscale: greyscale,
				lineart: lineart,
				type: 'notice'
			});
			if (modal === undefined) {
				modal = false;
			}
			$('body').append($dlg);

			// wrap callback in _.once():
			// only call callback once and not twice (button handler and close
			// event) but call it for the close event, if ESC or the x is hit
			if (callback !== undefined) {
				callback = _.once(callback);
			}

			var buttonlist = [{
				text: t('scanner', 'No'),
				click: function () {
					if (callback !== undefined) {
						callback(false, plugin.formArrayToObject($('form', $dlg).serializeArray()));
					}
					$(dialogId).ocdialog(t('scanner', 'close'));
				}
			}, {
				text: t('scanner', 'Yes'),
				click: function () {
					if (callback !== undefined) {
						callback(true, plugin.formArrayToObject($('form', $dlg).serializeArray()));
					}
					$(dialogId).ocdialog(t('scanner', 'close'));
				},
				defaultButton: true
			}
			];

			$(dialogId).ocdialog({
				closeOnEscape: true,
				modal: modal,
				buttons: buttonlist,
				close: function () {
					// callback is already fired if Yes/No is clicked directly
					if (callback !== undefined) {
						callback(false, plugin.formArrayToObject($('form', $dlg).serializeArray()));
					}
				}
			});
			OCdialogs.dialogsCounter++;
		});
	},
	formArrayToObject: function (formArray) {

		var returnArray = {};
		for (var i = 0; i < formArray.length; i++) {
			returnArray[formArray[i]['name']] = formArray[i]['value'];
		}
		return returnArray;
	}
};
OC.Plugins.register('OCA.Files.NewFileMenu', ScannerMenuPlugin);
