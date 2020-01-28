<?php # -*- coding: utf-8 -*-
declare(strict_types=1);


use OCA\Scanner\Sane\Param\Whitelist\ArrayWhitelist;
use OCA\Scanner\Sane\Param\Whitelist\ParamWhitelistFactory;
use OCA\Scanner\Sane\SaneBackend;

class SaneBackendTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider defaultTestData
     */
    public function testFromShellOutput($shellOutput, $expectedId, $acceptedParamValues)
    {

        $testee = SaneBackend::fromShellOutput($shellOutput);
        $this->assertInstanceOf(SaneBackend::class, $testee);
        $this->assertSame($expectedId, $testee->id());
//		$array = $testee->toArray();
//		var_dump($array);
    }

    /**
     * @dataProvider defaultTestData
     */
    public function testAcceptsParamValue($shellOutput, $expectedId, $acceptedParamValues, $rejectedParamValues)
    {
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

    public function defaultTestData()
    {
        yield [<<<'SHELL'
                                                                                                                                                                                                                                                                               
All options specific to device `hpaio:/net/Officejet_7500_E910?ip=192.168.188.38':
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


SHELL
            , 'hpaio:/net/Officejet_7500_E910?ip=192.168.188.38',
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
                'foo' => 'bar',
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

        yield [<<<'SHELL'
All options specific to device `plustek:libusb:002:002':
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

SHELL
            , 'plustek:libusb:002:002',
            [], []];

        yield [<<<'SHELL'

Options specific to device `test:0':
  Scan Mode:
    --mode Gray|Color [Gray]
        Selects the scan mode (e.g., lineart, monochrome, or color).
    --depth 1|8|16 [8]
        Number of bits per sample, typical values are 1 for "line-art" and 8
        for multibit scans.
    --hand-scanner[=(yes|no)] [no]
        Simulate a hand-scanner.  Hand-scanners do not know the image height a
        priori.  Instead, they return a height of -1.  Setting this option
        allows one to test whether a frontend can handle this correctly.  This
        option also enables a fixed width of 11 cm.
    --three-pass[=(yes|no)] [inactive]
        Simulate a three-pass scanner. In color mode, three frames are
        transmitted.
    --three-pass-order RGB|RBG|GBR|GRB|BRG|BGR [inactive]
        Set the order of frames in three-pass color mode.
    --resolution 1..1200dpi (in steps of 1) [50]
        Sets the resolution of the scanned image.
    --source Flatbed|Automatic Document Feeder [Flatbed]
        If Automatic Document Feeder is selected, the feeder will be 'empty'
        after 10 scans.
  Special Options:
    --test-picture Solid black|Solid white|Color pattern|Grid [Solid black]
        Select the kind of test picture. Available options:
        Solid black: fills the whole scan with black.
        Solid white: fills the whole scan with white.
        Color pattern: draws various color test patterns depending on the mode.
        Grid: draws a black/white grid with a width and height of 10 mm per
        square.
    --invert-endianess[=(yes|no)] [inactive]
        Exchange upper and lower byte of image data in 16 bit modes. This
        option can be used to test the 16 bit modes of frontends, e.g. if the
        frontend uses the correct endianness.
    --read-limit[=(yes|no)] [no]
        Limit the amount of data transferred with each call to sane_read().
    --read-limit-size 1..65536 (in steps of 1) [inactive]
        The (maximum) amount of data transferred with each call to
        sane_read().
    --read-delay[=(yes|no)] [no]
        Delay the transfer of data to the pipe.
    --read-delay-duration 1000..200000us (in steps of 1000) [inactive]
        How long to wait after transferring each buffer of data through the
        pipe.
    --read-return-value Default|SANE_STATUS_UNSUPPORTED|SANE_STATUS_CANCELLED|SANE_STATUS_DEVICE_BUSY|SANE_STATUS_INVAL|SANE_STATUS_EOF|SANE_STATUS_JAMMED|SANE_STATUS_NO_DOCS|SANE_STATUS_COVER_OPEN|SANE_STATUS_IO_ERROR|SANE_STATUS_NO_MEM|SANE_STATUS_ACCESS_DENIED [Default]
        Select the return-value of sane_read(). "Default" is the normal
        handling for scanning. All other status codes are for testing how the
        frontend handles them.
    --ppl-loss 0..128pel (in steps of 1) [0]
        The number of pixels that are wasted at the end of each line.
    --fuzzy-parameters[=(yes|no)] [no]
        Return fuzzy lines and bytes per line when sane_parameters() is called
        before sane_start().
    --non-blocking[=(yes|no)] [no]
        Use non-blocking IO for sane_read() if supported by the frontend.
    --select-fd[=(yes|no)] [no]
        Offer a select filedescriptor for detecting if sane_read() will return
        data.
    --enable-test-options[=(yes|no)] [no]
        Enable various test options. This is for testing the ability of
        frontends to view and modify all the different SANE option types.
    --print-options
        Print a list of all options.
  Geometry:
    -l 0..200mm (in steps of 1) [0]
        Top-left x position of scan area.
    -t 0..200mm (in steps of 1) [0]
        Top-left y position of scan area.
    -x 0..200mm (in steps of 1) [80]
        Width of scan-area.
    -y 0..200mm (in steps of 1) [100]
        Height of scan-area.
  Bool test options:
    --bool-soft-select-soft-detect[=(yes|no)] [inactive]
        (1/6) Bool test option that has soft select and soft detect (and
        advanced) capabilities. That's just a normal bool option.
    --bool-soft-select-soft-detect-emulated[=(yes|no)] [inactive]
        (5/6) Bool test option that has soft select, soft detect, and emulated
        (and advanced) capabilities.
    --bool-soft-select-soft-detect-auto[=(auto|yes|no)] [inactive]
        (6/6) Bool test option that has soft select, soft detect, and
        automatic (and advanced) capabilities. This option can be automatically
        set by the backend.
  Int test options:
    --int <int> [inactive]
        (1/6) Int test option with no unit and no constraint set.
    --int-constraint-range 4..192pel (in steps of 2) [inactive]
        (2/6) Int test option with unit pixel and constraint range set.
        Minimum is 4, maximum 192, and quant is 2.
    --int-constraint-word-list -42|-8|0|17|42|256|65536|16777216|1073741824bit [inactive]
        (3/6) Int test option with unit bits and constraint word list set.
    --int-constraint-array <int>,... [inactive]
        (4/6) Int test option with unit mm and using an array without
        constraints.
    --int-constraint-array-constraint-range 4..192dpi,... (in steps of 2) [inactive]
        (5/6) Int test option with unit dpi and using an array with a range
        constraint. Minimum is 4, maximum 192, and quant is 2.
    --int-constraint-array-constraint-word-list -42|-8|0|17|42|256|65536|16777216|1073741824%,... [inactive]
        (6/6) Int test option with unit percent and using an array with a word
        list constraint.
  Fixed test options:
    --fixed <float> [inactive]
        (1/3) Fixed test option with no unit and no constraint set.
    --fixed-constraint-range -42.17..32768us (in steps of 2) [inactive]
        (2/3) Fixed test option with unit microsecond and constraint range
        set. Minimum is -42.17, maximum 32767.9999, and quant is 2.0.
    --fixed-constraint-word-list -32.7|12.1|42|129.5 [inactive]
        (3/3) Fixed test option with no unit and constraint word list set.
  String test options:
    --string <string> [inactive]
        (1/3) String test option without constraint.
    --string-constraint-string-list First entry|Second entry|This is the very long third entry. Maybe the frontend has an idea how to display it [inactive]
        (2/3) String test option with string list constraint.
    --string-constraint-long-string-list First entry|Second entry|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46 [inactive]
        (3/3) String test option with string list constraint. Contains some
        more entries...
  Button test options:
    --button [inactive]
        (1/1) Button test option. Prints some text...

Type ``scanimage --help -d DEVICE'' to get list of all options for DEVICE.

List of available devices:
    test:0 test:1

SHELL
            , 'test:0',
            [
                'l' => 150,
                'resolution' => 100,
                'mode' => 'Gray'
            ], []];
    }
}
