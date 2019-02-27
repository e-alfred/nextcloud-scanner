<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param;


class ScanParameterFactory {
	public function create(
		string $name,
		string $description,
		string $optionString,
		string $default

	): ScanParam {
		/**
		 * There is a weird edge-case where something that would be a normal range param
		 * accepts an additional 'auto' param, looking like this:
		 *  -l auto|0..216.069mm [0]
		 *
		 * For now, we'll remove that option. We might be able to solve this with some compound ScanParam
		 * object that checks the range first, and then also checks a list for any input.
		 * This will be tricky to represent in the frontend, though, so I will deal with it later
		 * TODO: Implement 'auto' parameter option for range params.
		 */
		if ($this->isRange($optionString)) {
			$optionString = str_replace('auto|', '', $optionString);
		}
		switch (true) {
			case $this->isList($optionString):
				return new ListScanParam($name, $description, $optionString, $default);
			case $this->isRange($optionString):
				return new RangeScanParam($name, $description, $optionString, $default);
			default:
				return new NullScanParam();

		}
	}

	private function isRange(string $str): bool {
		return (bool)preg_match('/\.\./', $str);
	}

	private function isList(string $str): bool {
		return (bool)preg_match('/\|/', $str);
	}
}
