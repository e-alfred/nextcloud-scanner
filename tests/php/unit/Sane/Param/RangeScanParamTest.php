<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


use OCA\Scanner\Sane\Param\RangeScanParam;

class RangeScanParamTest extends PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider optionsTestData
	 */
	public function testOptions($optionString, $expected) {
		$testee = new RangeScanParam(
			'hurr',
			'durr',
			$optionString,
			'herp'
		);
		$options = $testee->options();
		$this->assertSame(array_values($expected), array_values($options));
	}

	public function optionsTestData() {

		yield['0..355.6mm', ['0','355.6']];
		yield['auto|0.299988..5', ['0.299988','5']];
		yield['-100..100%', ['-100','100']];
	}
}
