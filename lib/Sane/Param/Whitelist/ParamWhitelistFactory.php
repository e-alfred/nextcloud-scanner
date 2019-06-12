<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param\Whitelist;


class ParamWhitelistFactory {
	public function forBackendId(string $backendId): ParamWhitelist {
		//TODO This is supposed to be a per-backend whitelist provider in the future and should be expanded where it makes sense
		return new ArrayWhitelist([
			'resolution',
			'mode',
			'l',
			't',
			'x',
			'y',
		]);
	}
}
