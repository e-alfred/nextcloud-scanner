## Work in progress

* controller/scannercontroller.php:41: add some error handling on this result
* js/menu.js:24:                       Should this be ajax?
* js/menu.js:25:                       Scans are slow, show a spinner / bar
* lib/Storage/ScannerStorage.php:28:   This can happen because we don't refresh the file listing
* lib/Storage/ScannerStorage.php:32:   TODO: There's probably a way to stream this without the tempfile

* Add settings to control colour, resoultion, device url
  * Possibly make these show up in a JS popup per scan (with defaults)
