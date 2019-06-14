<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param;


class NullScanParam implements ScanParam {

	public function accepts(string $value): bool {
		return false;
	}

	public function options(): array {
		return [];
	}

	public function defaultValue(): string {
		return '';
	}

	public function description(): string {
		return '';
	}

	public function type(): string {
		return '';
	}

	public function name(): string {
		return '';
	}

	public function visibleByDefault(): bool {
		return false;
	}
}
