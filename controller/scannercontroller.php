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

use OCA\Scanner\Sane\BackendCollection;
use OCA\Scanner\Sane\ScanCommandArgs;
use OCA\Scanner\Storage\ScannerStorage;
use OCA\Scanner\Storage\StorageException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\Files\GenericFileException;
use OCP\Files\NotPermittedException;
use OCP\ICacheFactory;
use OCP\IRequest;

class ScannerController extends Controller {

	private $userId;
	/**
	 * @var ScannerStorage
	 */
	private $storage;
	private $cache;

	public function __construct(
		$AppName,
		IRequest $request,
		ScannerStorage $ScannerStorage,
		ICacheFactory $cacheFactory
	) {
		parent::__construct($AppName, $request);
		$this->storage = $ScannerStorage;
		$this->cache = $cacheFactory->createDistributed('scanner');
	}

	public function selfcheck() {
		$errors = [];

		foreach (['scanimage', 'pnmtojpeg'] as $binary) {
			if (!$this->commandExist($binary)) {
				$errors[] = sprintf('Cannot find required binary %s', $binary);
			}
		}

		if (empty($errors)) {
			return new DataResponse(
				[],
				Http::STATUS_OK);
		}

		return new DataResponse(
			$errors,
			Http::STATUS_BAD_GATEWAY);
	}

	private function commandExist($cmd): bool {
		$return = shell_exec(sprintf('command -v %s', escapeshellarg($cmd)));
		return null !== $return;
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
	public function scan($filename, $dir, $scanOptions): DataResponse {
		$backend = $this->getBackendCollection()->getByIndex(0);
		$scanOptions = (array)$scanOptions;
		$scanArgs = new ScanCommandArgs($scanOptions, $backend);

		$path = $dir . '/' . $filename;

		try {
			$result = $this->storage->scanFile($path, $scanArgs);
			$status = Http::STATUS_OK;
		} catch (StorageException $e) {
		} catch (GenericFileException $e) {
		} catch (NotPermittedException $e) {
			$result = $e->getMessage();
			$status = Http::STATUS_BAD_REQUEST;
		}
		return new DataResponse(['result' => $result], $status);
	}

	/**
	 * Fetch data of all available SANE backends and cache the results
	 *
	 * @return BackendCollection
	 */
	private function getBackendCollection(): BackendCollection {
		if (($backends = $this->cache->get('backends')) && $backends instanceof BackendCollection) {
			return $backends;
		}
		$backends = BackendCollection::fromShell();
		$this->cache->set('backends', $backends, 3600);
		return $backends;
	}

	public function backends() {
		return new DataResponse(
			$this->getBackendCollection()->toArray(),
			Http::STATUS_OK);
	}

	public function backend($id) {
		return new DataResponse(
			$this->getBackendCollection()->getByIndex((int)$id)->toArray(),
			Http::STATUS_OK);
	}

	public function preview($scanOptions) {

		$backend = $this->getBackendCollection()->getByIndex(0);
		$scanOptions = (array)$scanOptions;

		/**
		 * We always want a full preview at a rather low resolution,
		 * so ignore any scanOptions that would change that
		 */
		unset($scanOptions['x'], $scanOptions['y'], $scanOptions['l'], $scanOptions['t']);
		$scanOptions['resolution'] = $backend->getClosestAvailableResolution(30);
		$scanArgs = new ScanCommandArgs($scanOptions, $backend);
		$img = uniqid('scan', true);

		$command = "scanimage {$scanArgs} | pnmtojpeg > /tmp/{$img}";
		exec(
			$command,
			$output,
			$status
		);
		$data = file_get_contents("/tmp/{$img}");
		$data = 'data:image/jpeg;base64, ' . base64_encode($data);
		return new DataResponse(
			[
				'dpi' => $scanOptions['resolution'],
				'preview' => $data,
				'params' => $backend->toArray()
			],
			Http::STATUS_OK);

	}

}
