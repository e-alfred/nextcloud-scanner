<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param;


interface ScanParamListInterface {
	public function contains(string $key): bool;

	public function get(string $key): ScanParam;

	public function toArray(): array;
}
