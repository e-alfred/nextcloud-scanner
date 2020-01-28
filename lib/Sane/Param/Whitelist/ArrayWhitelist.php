<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param\Whitelist;


class ArrayWhitelist implements ParamWhitelist {
	/**
	 * @var array
	 */
	private $whitelist;

	public function __construct(array $whitelist = []) {

		$this->whitelist = $whitelist;
	}

	public function isWhitelisted(string $name): bool {
		return in_array($name, $this->whitelist, true);
	}
}
