<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param\Whitelist;


interface ParamWhitelist {
	public function isWhitelisted(string $name): bool;
}
