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

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\Scanner\Storage\ScannerStorage;

class PageController extends Controller {

	private $userId;

	public function __construct($AppName, IRequest $request, $ScannerStorage, $UserId){
		parent::__construct($AppName, $request);
    $this->storage = $ScannerStorage;
		$this->userId = $UserId;
	}

	/**
	 * Simply method that posts back the payload of the request
	 * @NoAdminRequired
	 */
	public function scan() {
    $filename = $_POST['filename'];
    $dir      = $_POST['dir'];
    $path     = $dir . "/" . $filename;

    $result = $this->storage->scanFile($path);

    // TODO: add some error handling on this result
    return new DataResponse(['result' => $result]);
	}


}
