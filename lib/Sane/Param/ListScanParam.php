<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


namespace OCA\Scanner\Sane\Param;


class ListScanParam implements ScanParam {
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
		foreach (['dpi', 'mm', 'bit'] as $item) {
			$optionString = $this->rtrimstr($item, $optionString);
		}
		$this->options = array_filter(explode('|', $optionString));

		$this->default = $default;
		$this->visibleByDefault = $visibleByDefault;
	}

	/**
	 * Removes a specified string off the end of a string
	 *
	 * @param string $remove
	 * @param string $str
	 * @return string
	 */
	private function rtrimstr(string $remove, string $str): string {
		$lenr = \strlen($remove);
		$lens = \strlen($str);
		if (strpos($str, $remove) !== $lens - $lenr) {
			return $str;
		}
		return substr($str, 0, $lens - $lenr);
	}

	public function accepts(string $value): bool {
		return \in_array($value, $this->options, true);
	}

	public function options(): array {
		return $this->options;
	}

	public function type(): string {
		return 'list';
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

	public function  visibleByDefault(): bool {
		return $this->visibleByDefault;
	}
}
