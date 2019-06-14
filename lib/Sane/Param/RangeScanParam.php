<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param;


class RangeScanParam implements ScanParam {
	private $name;
	private $description;
	private $options;
	private $default;
	/**
	 * @var bool
	 */
	private $visibleByDefault;

	public function __construct(string $name, string $description, string $optionString, string $default, bool $visibleByDefault) {

		$this->name = $name;
		$this->description = $description;
		$this->options = array_map(static function ($value) {
			return preg_replace('/[^0-9.\-]/', '', $value);
		},
			explode('..', $optionString));
		$this->default = $default;
		$this->visibleByDefault = $visibleByDefault;
	}

	public function accepts(string $value): bool {
		return $value >= $this->options[0] and $value <= $this->options[1];
	}

	public function options(): array {
		return $this->options;
	}

	public function type(): string {
		return 'range';
	}

	public function defaultValue(): string {
		return $this->default;
	}

	public function description(): string {
		return $this->description;
	}

	public function name(): string {
		return $this->name;
	}

	public function visibleByDefault(): bool {
		return $this->visibleByDefault;
	}
}
