<?php # -*- coding: utf-8 -*-


namespace OCA\Scanner\Sane\Param;


interface ScanParam {
	public function name(): string;

	public function accepts(string $value): bool;

	public function options(): array;

	public function defaultValue(): string;

	public function description(): string;

	public function type(): string;
}
