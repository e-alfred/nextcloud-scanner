<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane;


class ScanCommandArgs {
	/**
	 * @var array
	 */
	private $args;

	public function __construct(array $args, SaneBackend $backend) {

		$this->args = array_filter($args, function ($value, $key) use ($backend) {
			return $backend->acceptsParamValue((string)$key, (string)$value);
		}, ARRAY_FILTER_USE_BOTH);
	}

	public function __toString(): string {
		$str = '';
		array_walk($this->args, function ($value, $key) use (&$str) {
			$str .= " {$this->dashes($key)}{$key} {$value}";
		});
		return $str;
	}

	/**
	 * scanimage expects some args with one dash and other args with 2
	 *
	 * @param string $key
	 * @return string
	 */
	private function dashes(string $key): string {
		$singleDashes = ['x', 'y', 'l', 't'];
		return \in_array($key, $singleDashes, true) ? '-' : '--';
	}
}
