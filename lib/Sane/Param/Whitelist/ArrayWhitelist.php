<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param\Whitelist;


class ArrayWhitelist implements ParamWhitelist {
	/**
	 * @var array
	 */
	private $blacklist;

	public function __construct(array $blacklist = []) {

		$this->blacklist = $blacklist;
	}

	public function isWhitelisted(string $name): bool {
		return in_array($name, $this->blacklist, true);
	}
}
