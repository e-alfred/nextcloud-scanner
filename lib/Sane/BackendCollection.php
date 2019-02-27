<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane;


class BackendCollection {
	/**
	 * @var SaneBackend[]
	 */
	private $saneBackends;

	public function __construct(array $saneBackends) {

		$this->saneBackends = $saneBackends;
	}

	public static function fromShell(): BackendCollection {
		exec('scanimage -L', $lines);
		return self::fromShellOutput($lines);
	}

	public static function fromShellOutput(array $shellOutput): BackendCollection {
		$backendIds = [];
		foreach ($shellOutput as $line) {
			preg_match('/`(.*)\'/', $line, $matches);
			$backendIds[] = $matches[1];
		}
		$backends = [];
		foreach ($backendIds as $backendId) {
			try {
				$backend = SaneBackend::fromShell($backendId);
				$backends[$backendId] = $backend;
			} catch (Exception\InvalidArgumentException $e) {
				continue;
			}
		}
		return new self($backends);
	}

	public function getAll(): array {
		return $this->saneBackends;
	}

	public function getByIndex(int $idx): SaneBackend {
		$indexed = array_values($this->saneBackends);
		return $indexed[$idx];
	}

	public function toArray(): array {
		return array_values(array_map(function (SaneBackend $backend) {
			return $backend->toArray();
		}, $this->saneBackends));
	}
}
