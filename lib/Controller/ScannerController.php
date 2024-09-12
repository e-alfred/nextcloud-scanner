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

namespace OCA\Scanner\Controller;

use OCA\Scanner\Storage\ScannerStorage;
use OCA\Scanner\Storage\StorageException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\Files\GenericFileException;
use OCP\Files\NotPermittedException;
use OCP\IRequest;

class ScannerController extends Controller {

	private $userId;
	/**
	 * @var ScannerStorage
	 */
	private $storage;

	public function __construct(
		$AppName,
		IRequest $request,
		ScannerStorage $ScannerStorage,
		$UserId
	) {
		parent::__construct($AppName, $request);
		$this->storage = $ScannerStorage;
		$this->userId = $UserId;
	}

	/**
	 * Simply method that posts back the payload of the request
	 *
	 * @NoAdminRequired
	 * @param $filename
	 * @param $dir
	 * @param $scanOptions
	 * @return DataResponse
	 */
	public function scan($filename, $dir, $scanOptions) {
		$status = Http::STATUS_OK; 
		$result = 'result';
		$path = $dir . '/' . $filename;
		$mode = (int)$scanOptions['mode'];
		$resolution = (int)$scanOptions['resolution'];
		try {
			$result = $this->storage->scanFile($path, $mode, $resolution);
			$status = Http::STATUS_OK;
		} catch (StorageException $e) {
			$result = $e->getMessage();
			$status = Http::STATUS_BAD_REQUEST;
		} catch (GenericFileException $e) {
		} catch (NotPermittedException $e) {
			$result = $e->getMessage();
			$status = Http::STATUS_BAD_REQUEST;
		}
		return new DataResponse(['result' => $result], $status);
	}


}
