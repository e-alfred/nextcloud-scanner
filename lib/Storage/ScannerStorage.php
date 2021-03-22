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
use OC\Files\Node\Folder;
use OCP\Files\GenericFileException;
use OCP\Files\NotPermittedException;
use \OCP\IL10N;

class StorageException extends Exception {
}

class ScannerStorage {

	private $trans;

	private $storage;
	private $modes = [
		0 => 'Color',
		1 => 'Gray',
		2 => 'Lineart'
	];

	public function __construct(Folder $storage, IL10N $trans) {
		$this->storage = $storage;
		$this->trans = $trans;
	}

	public function getLanguageCode() {
		return $this->trans->getLanguageCode();
  }

	/**
	 * @param $name
	 * @param string $mode
	 * @param int $resolution
	 * @return string
	 * @throws GenericFileException
	 * @throws NotPermittedException
	 * @throws StorageException
	 */
	public function scanFile($name, $mode = 0, $resolution = 300) {
		if ($this->storage->nodeExists($name)) {
			// TODO: This can happen because we don't refresh the file listing
			throw new StorageException($this->trans->t('File already exists'));
		}
		$file = $this->storage->newFile($name);
		// TODO: There's probably a way to stream this without the tempfile
		exec(
			"sudo scanimage --mode {$this->modes[$mode]} --resolution {$resolution} -x 215 -y 297| pnmtojpeg > /tmp/img",
			$output,
			$status
		);
		if ($status) {
			throw new StorageException($this->trans->t('Something went wrong while attempting to scan'));
		}
		$data = file_get_contents('/tmp/img');
		$file->putContent($data);
		return $this->trans->t('Success');
	}

}
