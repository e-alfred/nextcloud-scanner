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
use OCA\Scanner\Sane\ScanCommandArgs;
use OCP\Files\GenericFileException;
use OCP\Files\NotPermittedException;

class StorageException extends Exception {
}

class ScannerStorage {

	private $storage;

	public function __construct(Folder $storage) {
		$this->storage = $storage;
	}

	/**
	 * @param $name
	 * @param ScanCommandArgs $args
	 * @return string
	 * @throws GenericFileException
	 * @throws NotPermittedException
	 * @throws StorageException
	 */
	public function scanFile($name, ScanCommandArgs $args) {
		if ($this->storage->nodeExists($name)) {
			throw new StorageException('File already exists');
		}
		$img = uniqid('scan', true);
		$command = "scanimage {$args} | pnmtojpeg > /tmp/{$img}";
		// TODO: There's probably a way to stream this without the tempfile
		exec(
			$command,
			$output,
			$status
		);
		if ($status) {
			throw new StorageException('Something went wrong while attempting to scan');
		}
		$data = file_get_contents("/tmp/{$img}");
		$file = $this->storage->newFile($name);
		$file->putContent($data);
		return 'success';
	}

}
