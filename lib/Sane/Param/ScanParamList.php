<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param;


class ScanParamList implements ScanParamListInterface {
	/**
	 * @var ScanParam[]
	 */
	private $scanParams;

	public function __construct(array $scanParams) {

		$this->scanParams = $scanParams;
	}

	public function contains(string $key): bool {
		foreach ($this->scanParams as $scanParam) {
			if ($scanParam->name() === $key) {
				return true;
			}
		}
		return false;
	}

	public function get(string $key): ScanParam {
		foreach ($this->scanParams as $scanParam) {
			if ($scanParam->name() === $key) {
				return $scanParam;
			}
		}
		return new NullScanParam();
	}

	public function toArray(): array {
		$arr = [];
		foreach ($this->scanParams as $scanParam) {
			if ($scanParam instanceof NullScanParam) {
				continue;
			}
			$arr[$scanParam->name()] = [
				'description' => $scanParam->description(),
				'type' => $scanParam->type(),
				'default' => $scanParam->defaultValue(),
				'options' => $scanParam->options(),
			];
		}
		return $arr;
	}
}
