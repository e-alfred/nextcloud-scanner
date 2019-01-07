<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

namespace OCA\Scanner\Sane;


class SaneBackend {

	/**
	 * @var array
	 */
	private $params;
	/**
	 * @var string
	 */
	private $id;

	public function __construct(string $id, array $params) {

		$this->params = $params;
		$this->id = $id;
	}

	public static function fromShell(string $id): self {

		exec("scanimage -A -d {$id}", $result);
		$result = implode("\n", $result);
		preg_match_all('/\s+--?(\S+)\s(.*)\s+\[(.*)\]/', $result, $matches);
		list(, $parameterNames, $options, $defaults) = $matches;
		$params = [];

		array_walk($parameterNames, function ($name, $idx) use (&$params, $options, $defaults) {

			/**
			 * Remove some bogus params that are either deprecated or nonsensical.
			 * Let's hope this won't lead to collisions between backends...
			 */
			$blacklist = [
				'jpeg-quality'
			];
			if (\in_array($name, $blacklist, true)) {
				return;
			}

			$params[$name] = [
				'options' => $options[$idx],
				'default' => $defaults[$idx]
			];
		});

		return new self($id, $params);

	}

	public function acceptsParamValue(string $paramName, string $value): bool {
		if (!$this->acceptsParam($paramName)) {
			return false;
		}
		$options = $this->params[$paramName]['options'];
		$list = $this->trimUnits(explode('|', $options));
		if (\count($list) > 1) {
			return \in_array($value, $list, true);
		}

		$range = $this->trimUnits(explode('..', $options));
		if (\count($range) > 1) {
			$min = (float)$range[0];
			$max = (float)$range[1];
			$val = (float)$value;
			return ($val >= $min && $val <= $max);
		}
		return false;
	}

	public function acceptsParam(string $paramName): bool {
		return array_key_exists($paramName, $this->params);
	}

	private function trimUnits(array $array): array {
		$array[\count($array) - 1] = str_replace(['mm', 'dpi'], '', $array[\count($array) - 1]);
		return $array;
	}

	public function toArray(): array {
		return ['id' => $this->id, 'params' => $this->params];
	}

	public function getClosestAvailableResolution(int $desired): int {
		if (!isset($this->params['resolution'])) {
			//TODO exception?
			return 0;
		}
		$options = $this->params['resolution']['options'];
		$list = explode('|', $options);
		$desiredResolution = 30;
		$chosenResolution = $desiredResolution;
		if (!empty($list)) {
			foreach ($list as $option) {
				if ($option > $desiredResolution) {
					$chosenResolution = $option;
					break;
				}
			}
		} else {
			$range = explode('..', $options);
			$min = $range[0];
			$chosenResolution = ($min > $desiredResolution) ? $min : $desiredResolution;
		}

		return (int)$chosenResolution;
	}
}
