<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


use OCA\Scanner\Sane\Param\Whitelist\ArrayWhitelist;
use OCA\Scanner\Sane\Param\Whitelist\ParamWhitelistFactory;
use OCA\Scanner\Sane\SaneBackend;

class SaneBackendTest extends PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider defaultTestData
	 */
	public function testFromShellOutput($shellOutput, $expectedId, $acceptedParamValues) {

		$testee = SaneBackend::fromShellOutput($shellOutput);
		$this->assertInstanceOf(SaneBackend::class, $testee);
		$this->assertSame($expectedId, $testee->id());
//		$array = $testee->toArray();
//		var_dump($array);
	}

	/**
	 * @dataProvider defaultTestData
	 */
	public function testAcceptsParamValue($shellOutput, $expectedId, $acceptedParamValues, $rejectedParamValues) {
		$whitelist = array_keys($acceptedParamValues);
		$whitelistFactory = $this->createMock(ParamWhitelistFactory::class);
		$paramWhitelist = new ArrayWhitelist($whitelist);
		$whitelistFactory->method('forBackendId')->with($expectedId)->willReturn($paramWhitelist);
		$testee = SaneBackend::fromShellOutput($shellOutput, $whitelistFactory);
		foreach ($acceptedParamValues as $key => $value) {
			$this->assertTrue($testee->acceptsParamValue($key, (string)$value), "Parameter:'{$key}', Value:'{$value}'");
		}

		foreach ($rejectedParamValues as $key => $value) {
			$this->assertFalse($testee->acceptsParamValue($key, (string)$value), "Parameter:'{$key}', Value:'{$value}'");

		}
	}

	public function defaultTestData() {
		yield ['                                                                                                                                                                                                                                                                               
All options specific to device `hpaio:/net/Officejet_7500_E910?ip=192.168.188.38\':
  Scan mode:
    --mode Lineart|Gray|Color [Lineart]
        Selects the scan mode (e.g., lineart, monochrome, or color).
    --resolution 75|100|200|300|600dpi [75]
        Sets the resolution of the scanned image.
    --source Flatbed|ADF [Flatbed]
        Selects the scan source (such as a document-feeder).
  Advanced:
    --brightness 0..2000 [1000]
        Controls the brightness of the acquired image.
    --contrast 0..2000 [1000]
        Controls the contrast of the acquired image.
    --compression JPEG [JPEG]
        Selects the scanner compression method for faster scans, possibly at
        the expense of image quality.
    --jpeg-quality 0..100 [inactive]
        Sets the scanner JPEG compression factor. Larger numbers mean better
        compression, and smaller numbers mean better image quality.
  Geometry:
    -l 0..215.9mm [0]
        Top-left x position of scan area.
    -t 0..355.6mm [0]
        Top-left y position of scan area.
    -x 0..215.9mm [215.9]
        Width of scan-area.
    -y 0..355.6mm [355.6]
        Height of scan-area.

', 'hpaio:/net/Officejet_7500_E910?ip=192.168.188.38',
			[
				'l' => 210,
				'resolution' => 100,
				'brightness' => 250,
				'mode' => 'Gray'
			],
			[
				'l' => 300,
				'resolution' => 101,
				'brightness' => 3000,
				'compression' => 'nvkdjvejk',
				'foo' => 'bar'
			]
		];
		yield ['                                                                                                                                                                                                                                                                               
All options specific to device `net:192.168.24.123:pixma\':                                                                                                                                                                                                       
  Scan mode:                                                                                                                                                                                                                                                                   
    --resolution auto||75|150|300|600dpi [75]                                                                                                                                                                                                                                  
        Sets the resolution of the scanned image.                                                                                                                                                                                                                              
    --mode auto|Color|Gray|Lineart [Color]                                                                                                                                                                                                                                     
        Selects the scan mode (e.g., lineart, monochrome, or color).                                                                                                                                                                                                           
    --source Flatbed [Flatbed]                                                                                                                                                                                                                                                 
        Selects the scan source (such as a document-feeder). Set source before                                                                                                                                                                                                 
        mode and resolution. Resets mode and resolution to auto values.                                                                                                                                                                                                        
    --button-controlled[=(yes|no)] [no]                                                                                                                                                                                                                                        
        When enabled, scan process will not start immediately. To proceed,                                                                                                                                                                                                     
        press "SCAN" button (for MP150) or "COLOR" button (for other models).                                                                                                                                                                                                  
        To cancel, press "GRAY" button.
  Gamma:
    --custom-gamma[=(auto|yes|no)] [yes]
        Determines whether a builtin or a custom gamma-table should be used.
    --gamma-table auto|0..255,...
        Gamma-correction table.  In color mode this option equally affects the
        red, green, and blue channels simultaneously (i.e., it is an intensity
        gamma table).
    --gamma auto|0.299988..5 [2.2]
        Changes intensity of midtones
  Geometry:
    -l auto|0..216.069mm [0]
        Top-left x position of scan area.
    -t auto|0..297.011mm [0]
        Top-left y position of scan area.
    -x auto|0..216.069mm [216.069]
        Width of scan-area.
    -y auto|0..297.011mm [297.011]
        Height of scan-area.
  Buttons:
    --button-update
        Update button state',
			'net:192.168.24.123:pixma',
			[
				'l' => 100
			], []
		];

		yield ['
All options specific to device `plustek:libusb:002:002\':
  Scan Mode:
    --mode Lineart|Gray|Color [Color]
        Selects the scan mode (e.g., lineart, monochrome, or color).
    --depth 8|14bit [8]
        Number of bits per sample, typical values are 1 for "line-art" and 8
        for multibit scans.
    --source Normal|Transparency|Negative [Normal]
        Selects the scan source (such as a document-feeder).
    --resolution 50..1200dpi [50]
        Sets the resolution of the scanned image.
    --preview[=(yes|no)] [no]
        Request a preview-quality scan.
  Geometry:
    -l 0..215mm [0]
        Top-left x position of scan area.
    -t 0..297mm [0]
        Top-left y position of scan area.
    -x 0..215mm [103]
        Width of scan-area.
    -y 0..297mm [76.21]
        Height of scan-area.
  Enhancement:
    --brightness -100..100% (in steps of 1) [0]
        Controls the brightness of the acquired image.
    --contrast -100..100% (in steps of 1) [0]
        Controls the contrast of the acquired image.
    --custom-gamma[=(yes|no)] [no]
        Determines whether a builtin or a custom gamma-table should be used.
    --gamma-table 0..255,... [inactive]
        Gamma-correction table.  In color mode this option equally affects the
        red, green, and blue channels simultaneously (i.e., it is an intensity
        gamma table).
    --red-gamma-table 0..255,... [inactive]
        Gamma-correction table for the red band.
    --green-gamma-table 0..255,... [inactive]
        Gamma-correction table for the green band.
    --blue-gamma-table 0..255,... [inactive]
        Gamma-correction table for the blue band.
  Device-Settings:
    --lamp-switch[=(yes|no)] [no]
        Manually switching the lamp(s).
    --lampoff-time 0..999 (in steps of 1) [300]
        Lampoff-time in seconds.
    --lamp-off-at-exit[=(yes|no)] [yes]
        Turn off lamp when program exits
    --warmup-time -1..999 (in steps of 1) [-1]
        Warmup-time in seconds.
    --lamp-off-during-dcal[=(yes|no)] [inactive]
        Always switches lamp off when doing dark calibration.
    --calibration-cache[=(yes|no)] [no]
        Enables or disables calibration data cache.
    --speedup-switch[=(yes|no)] [yes]
        Enables or disables speeding up sensor movement.
    --calibrate [inactive]
        Performs calibration
  Analog frontend:
    --red-gain -1..63 (in steps of 1) [-1]
        Red gain value of the AFE
    --green-gain -1..63 (in steps of 1) [-1]
        Green gain value of the AFE
    --blue-gain -1..63 (in steps of 1) [-1]
        Blue gain value of the AFE
    --red-offset -1..63 (in steps of 1) [-1]
        Red offset value of the AFE
    --green-offset -1..63 (in steps of 1) [-1]
        Green offset value of the AFE
    --blue-offset -1..63 (in steps of 1) [-1]
        Blue offset value of the AFE
    --redlamp-off -1..16363 (in steps of 1) [inactive]
        Defines red lamp off parameter
    --greenlamp-off -1..16363 (in steps of 1) [inactive]
        Defines green lamp off parameter
    --bluelamp-off -1..16363 (in steps of 1) [inactive]
        Defines blue lamp off parameter
  Buttons:
    --button 0[=(yes|no)] [no] [hardware]
        This option reflects the status of the scanner buttons.
    --button 1[=(yes|no)] [inactive]
        This option reflects the status of the scanner buttons.
    --button 2[=(yes|no)] [inactive]
        This option reflects the status of the scanner buttons.
    --button 3[=(yes|no)] [inactive]
        This option reflects the status of the scanner buttons.
    --button 4[=(yes|no)] [inactive]
        This option reflects the status of the scanner buttons.
', 'plustek:libusb:002:002',
			[], []];
	}
}
