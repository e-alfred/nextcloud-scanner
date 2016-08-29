<?php
/**
 * Nextcloud - scanner
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Greg Sutcliffe <nextcloud@emeraldreverie.org>
 * @copyright Greg Sutcliffe 2016
 */

namespace OCA\Scanner\Storage;

use Exception;

class StorageException extends Exception {}

class ScannerStorage {

  private $storage;

  public function __construct($storage){
    $this->storage = $storage;
  }

  public function scanFile($name) {
    if($this->storage->nodeExists($name)) {
      // TODO: This can happen because we don't refresh the file listing
      throw new StorageException('File already exists');
    } else {
      $file = $this->storage->newFile($name);
      // TODO: There's probably a way to stream this without the tempfile
      exec("scanimage --mode Gray --resolution 150 | pnmtojpeg > /tmp/img");
      $data = file_get_contents('/tmp/img');
      $file->putContent($data);
      return 'success';
    }
  }

}
