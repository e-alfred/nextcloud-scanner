<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


use OCA\Scanner\Sane\Param\ListScanParam;

class ListScanParamTest extends PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider optionsTestData
	 */
	public function testOptions($optionString, $expected) {
		$testee = new ListScanParam(
			'hurr',
			'durr',
			$optionString,
			'herp',
			true
		);
		$options = $testee->options();
		$this->assertSame(array_values($expected), array_values($options));
	}

	public function optionsTestData() {

		yield['Lineart|Gray|Color', ['Lineart', 'Gray', 'Color']];
		yield['75|100|200|300|600dpi', ['75', '100', '200', '300', '600']];
		yield['auto||75|150|300|600dpi', ['auto', '75', '150', '300', '600']];
		yield['8|14bit', ['8', '14']];
	}
}
