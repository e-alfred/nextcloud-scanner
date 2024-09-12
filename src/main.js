import { getCurrentUser } from '@nextcloud/auth'
import { addNewFileMenuEntry, Permission, File } from '@nextcloud/files';
import { emit } from '@nextcloud/event-bus'
import { getUniqueName } from './utils.js';

function formArrayToObject(formArray) {
  var returnArray = {};
  for (var i = 0; i < formArray.length; i++) {
    returnArray[formArray[i]['name']] = formArray[i]['value'];
  }
  return returnArray;
}

function getTemplate(tplFile) {
  var defer = $.Deferred();
  var self = this;
  $.get(OC.filePath('scanner', 'templates', tplFile), function (tmpl) {
    // self.$messageTemplate = $(tmpl);
    defer.resolve($(tmpl));
  })
    .fail(function (jqXHR, textStatus, errorThrown) {
      defer.reject(jqXHR.status, errorThrown);
    });
  return defer.promise();
};

function scanOptionsModal(mode, color, greyscale, lineart, resolution, text, title, name, filename, callback, modal) {
  return $.when(getTemplate('optionsdialog.html')).then(function ($tmpl) {
    var dialogName = 'oc-dialog-' + OC.dialogs.dialogsCounter + '-content';
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
      name: name,
      filename: filename,
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
          callback(false, formArrayToObject($('form', $dlg).serializeArray()));
        }
        $(dialogId).ocdialog('close');
      }
    }, {
      text: t('scanner', 'Yes'),
      click: function () {
        if (callback !== undefined) {
          callback(true, formArrayToObject($('form', $dlg).serializeArray()));
        }
        $(dialogId).ocdialog('close');
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
          callback(false, formArrayToObject($('form', $dlg).serializeArray()));
        }
      }
    });
    OC.dialogs.dialogsCounter++;
  });
};

const entry = {
  id: 'scanner',
  displayName: t('scanner', 'Scan Image'),
  templateName: 'scan.jpg',
  iconClass: 'icon-filetype-scanner',
  fileType: 'file',
  async handler(context, content) {
    const defaultName = 'scan.jpg';
    const contentNames = content.map((node) => node.basename);
    const name = getUniqueName(defaultName, contentNames);
    scanOptionsModal(
      t('scanner', 'Mode'),
      t('scanner', 'Color'),
      t('scanner', 'Greyscale'),
      t('scanner', 'Lineart'),
      t('scanner', 'Resolution'),
      t('scanner', 'Please adjust scan parameters'),
      t('scanner', 'Scan Options'),
      t('scanner', 'Filename'),
      name,
    function (result, formData) {
      if (!result) {
        OC.Notification.showTemporary(t('scanner', 'Scan aborted.'));
        return;
      }
      var dir = context.path;

      OC.Notification.showTemporary(t('scanner', 'Scan started.'));

      const fileName = formData.filename ? formData.filename : name;
      $.ajax({
        url: OC.generateUrl('/apps/scanner/scan'),
        async: true,
        type: 'POST',
        data: {
          filename: fileName,
          dir: dir,
          scanOptions: formData
        },
        success: function (data) {
          const file = new File({
            source: context.source + '/' + fileName,
            id: data.result,
            mtime: new Date(),
            mime: 'image/jpeg',
            owner: getCurrentUser()?.uid || null,
            permissions: Permission.ALL,
            root: context?.root || '/files/' + getCurrentUser()?.uid,
          });
          emit('files:node:created', file);
          OC.Notification.showTemporary(t('scanner', 'Scan complete'));
        },
        error: function (data) {
          OC.Notification.showTemporary(data.result);
        }
      });
    });

  },
};

addNewFileMenuEntry(entry);