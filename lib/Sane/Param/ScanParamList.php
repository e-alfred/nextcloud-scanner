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
		foreach ($this->scanParams as $param) {
			if ($param instanceof NullScanParam) {
				continue;
			}
			$arr[$param->name()] = [
				'description' => $param->description(),
				'type' => $param->type(),
				'default' => $param->defaultValue(),
				'options' => $param->options(),
				'visibleByDefault' => $param->visibleByDefault()
			];
		}
		return $arr;
	}
}
