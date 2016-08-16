<?php

namespace RTLWORKS;

/**
 * This is a class to hold the algorithms
 * for language and directionality detection.
 *
 * The code here is taken and adapted mostly from
 * https://gerrit.wikimedia.org/r/#/c/221774/5/languages/Language.php
 */
class LanguageDetector {
	static private $lre = "\xE2\x80\xAA"; // U+202A LEFT-TO-RIGHT EMBEDDING
	static private $rle = "\xE2\x80\xAB"; // U+202B RIGHT-TO-LEFT EMBEDDING
	static private $pdf = "\xE2\x80\xAC"; // U+202C POP DIRECTIONAL FORMATTING}
	static private $strongDirRegex = array(
		'ltr' => '/[\x{41}-\x{5a}\x{61}-\x{7a}\x{aa}\x{b5}\x{ba}\x{c0}-\x{d6}\x{d8}-\x{f6}\x{f8}-\x{2b8}\x{2bb}-\x{2c1}\x{2d0}\x{2d1}\x{2e0}-\x{2e4}\x{2ee}\x{370}-\x{373}\x{376}\x{377}\x{37a}-\x{37d}\x{37f}\x{386}\x{388}-\x{38a}\x{38c}\x{38e}-\x{3a1}\x{3a3}-\x{3f5}\x{3f7}-\x{482}\x{48a}-\x{52f}\x{531}-\x{556}\x{559}-\x{55f}\x{561}-\x{587}\x{589}\x{903}-\x{939}\x{93b}\x{93d}-\x{940}\x{949}-\x{94c}\x{94e}-\x{950}\x{958}-\x{961}\x{964}-\x{980}\x{982}\x{983}\x{985}-\x{98c}\x{98f}\x{990}\x{993}-\x{9a8}\x{9aa}-\x{9b0}\x{9b2}\x{9b6}-\x{9b9}\x{9bd}-\x{9c0}\x{9c7}\x{9c8}\x{9cb}\x{9cc}\x{9ce}\x{9d7}\x{9dc}\x{9dd}\x{9df}-\x{9e1}\x{9e6}-\x{9f1}\x{9f4}-\x{9fa}\x{a03}\x{a05}-\x{a0a}\x{a0f}\x{a10}\x{a13}-\x{a28}\x{a2a}-\x{a30}\x{a32}\x{a33}\x{a35}\x{a36}\x{a38}\x{a39}\x{a3e}-\x{a40}\x{a59}-\x{a5c}\x{a5e}\x{a66}-\x{a6f}\x{a72}-\x{a74}\x{a83}\x{a85}-\x{a8d}\x{a8f}-\x{a91}\x{a93}-\x{aa8}\x{aaa}-\x{ab0}\x{ab2}\x{ab3}\x{ab5}-\x{ab9}\x{abd}-\x{ac0}\x{ac9}\x{acb}\x{acc}\x{ad0}\x{ae0}\x{ae1}\x{ae6}-\x{af0}\x{af9}\x{b02}\x{b03}\x{b05}-\x{b0c}\x{b0f}\x{b10}\x{b13}-\x{b28}\x{b2a}-\x{b30}\x{b32}\x{b33}\x{b35}-\x{b39}\x{b3d}\x{b3e}\x{b40}\x{b47}\x{b48}\x{b4b}\x{b4c}\x{b57}\x{b5c}\x{b5d}\x{b5f}-\x{b61}\x{b66}-\x{b77}\x{b83}\x{b85}-\x{b8a}\x{b8e}-\x{b90}\x{b92}-\x{b95}\x{b99}\x{b9a}\x{b9c}\x{b9e}\x{b9f}\x{ba3}\x{ba4}\x{ba8}-\x{baa}\x{bae}-\x{bb9}\x{bbe}\x{bbf}\x{bc1}\x{bc2}\x{bc6}-\x{bc8}\x{bca}-\x{bcc}\x{bd0}\x{bd7}\x{be6}-\x{bf2}\x{c01}-\x{c03}\x{c05}-\x{c0c}\x{c0e}-\x{c10}\x{c12}-\x{c28}\x{c2a}-\x{c39}\x{c3d}\x{c41}-\x{c44}\x{c58}-\x{c5a}\x{c60}\x{c61}\x{c66}-\x{c6f}\x{c7f}\x{c82}\x{c83}\x{c85}-\x{c8c}\x{c8e}-\x{c90}\x{c92}-\x{ca8}\x{caa}-\x{cb3}\x{cb5}-\x{cb9}\x{cbd}-\x{cc4}\x{cc6}-\x{cc8}\x{cca}\x{ccb}\x{cd5}\x{cd6}\x{cde}\x{ce0}\x{ce1}\x{ce6}-\x{cef}\x{cf1}\x{cf2}\x{d02}\x{d03}\x{d05}-\x{d0c}\x{d0e}-\x{d10}\x{d12}-\x{d3a}\x{d3d}-\x{d40}\x{d46}-\x{d48}\x{d4a}-\x{d4c}\x{d4e}\x{d57}\x{d5f}-\x{d61}\x{d66}-\x{d75}\x{d79}-\x{d7f}\x{d82}\x{d83}\x{d85}-\x{d96}\x{d9a}-\x{db1}\x{db3}-\x{dbb}\x{dbd}\x{dc0}-\x{dc6}\x{dcf}-\x{dd1}\x{dd8}-\x{ddf}\x{de6}-\x{def}\x{df2}-\x{df4}\x{e01}-\x{e30}\x{e32}\x{e33}\x{e40}-\x{e46}\x{e4f}-\x{e5b}\x{e81}\x{e82}\x{e84}\x{e87}\x{e88}\x{e8a}\x{e8d}\x{e94}-\x{e97}\x{e99}-\x{e9f}\x{ea1}-\x{ea3}\x{ea5}\x{ea7}\x{eaa}\x{eab}\x{ead}-\x{eb0}\x{eb2}\x{eb3}\x{ebd}\x{ec0}-\x{ec4}\x{ec6}\x{ed0}-\x{ed9}\x{edc}-\x{edf}\x{f00}-\x{f17}\x{f1a}-\x{f34}\x{f36}\x{f38}\x{f3e}-\x{f47}\x{f49}-\x{f6c}\x{f7f}\x{f85}\x{f88}-\x{f8c}\x{fbe}-\x{fc5}\x{fc7}-\x{fcc}\x{fce}-\x{fda}\x{1000}-\x{102c}\x{1031}\x{1038}\x{103b}\x{103c}\x{103f}-\x{1057}\x{105a}-\x{105d}\x{1061}-\x{1070}\x{1075}-\x{1081}\x{1083}\x{1084}\x{1087}-\x{108c}\x{108e}-\x{109c}\x{109e}-\x{10c5}\x{10c7}\x{10cd}\x{10d0}-\x{1248}\x{124a}-\x{124d}\x{1250}-\x{1256}\x{1258}\x{125a}-\x{125d}\x{1260}-\x{1288}\x{128a}-\x{128d}\x{1290}-\x{12b0}\x{12b2}-\x{12b5}\x{12b8}-\x{12be}\x{12c0}\x{12c2}-\x{12c5}\x{12c8}-\x{12d6}\x{12d8}-\x{1310}\x{1312}-\x{1315}\x{1318}-\x{135a}\x{1360}-\x{137c}\x{1380}-\x{138f}\x{13a0}-\x{13f5}\x{13f8}-\x{13fd}\x{1401}-\x{167f}\x{1681}-\x{169a}\x{16a0}-\x{16f8}\x{1700}-\x{170c}\x{170e}-\x{1711}\x{1720}-\x{1731}\x{1735}\x{1736}\x{1740}-\x{1751}\x{1760}-\x{176c}\x{176e}-\x{1770}\x{1780}-\x{17b3}\x{17b6}\x{17be}-\x{17c5}\x{17c7}\x{17c8}\x{17d4}-\x{17da}\x{17dc}\x{17e0}-\x{17e9}\x{1810}-\x{1819}\x{1820}-\x{1877}\x{1880}-\x{18a8}\x{18aa}\x{18b0}-\x{18f5}\x{1900}-\x{191e}\x{1923}-\x{1926}\x{1929}-\x{192b}\x{1930}\x{1931}\x{1933}-\x{1938}\x{1946}-\x{196d}\x{1970}-\x{1974}\x{1980}-\x{19ab}\x{19b0}-\x{19c9}\x{19d0}-\x{19da}\x{1a00}-\x{1a16}\x{1a19}\x{1a1a}\x{1a1e}-\x{1a55}\x{1a57}\x{1a61}\x{1a63}\x{1a64}\x{1a6d}-\x{1a72}\x{1a80}-\x{1a89}\x{1a90}-\x{1a99}\x{1aa0}-\x{1aad}\x{1b04}-\x{1b33}\x{1b35}\x{1b3b}\x{1b3d}-\x{1b41}\x{1b43}-\x{1b4b}\x{1b50}-\x{1b6a}\x{1b74}-\x{1b7c}\x{1b82}-\x{1ba1}\x{1ba6}\x{1ba7}\x{1baa}\x{1bae}-\x{1be5}\x{1be7}\x{1bea}-\x{1bec}\x{1bee}\x{1bf2}\x{1bf3}\x{1bfc}-\x{1c2b}\x{1c34}\x{1c35}\x{1c3b}-\x{1c49}\x{1c4d}-\x{1c7f}\x{1cc0}-\x{1cc7}\x{1cd3}\x{1ce1}\x{1ce9}-\x{1cec}\x{1cee}-\x{1cf3}\x{1cf5}\x{1cf6}\x{1d00}-\x{1dbf}\x{1e00}-\x{1f15}\x{1f18}-\x{1f1d}\x{1f20}-\x{1f45}\x{1f48}-\x{1f4d}\x{1f50}-\x{1f57}\x{1f59}\x{1f5b}\x{1f5d}\x{1f5f}-\x{1f7d}\x{1f80}-\x{1fb4}\x{1fb6}-\x{1fbc}\x{1fbe}\x{1fc2}-\x{1fc4}\x{1fc6}-\x{1fcc}\x{1fd0}-\x{1fd3}\x{1fd6}-\x{1fdb}\x{1fe0}-\x{1fec}\x{1ff2}-\x{1ff4}\x{1ff6}-\x{1ffc}\x{200e}\x{2071}\x{207f}\x{2090}-\x{209c}\x{2102}\x{2107}\x{210a}-\x{2113}\x{2115}\x{2119}-\x{211d}\x{2124}\x{2126}\x{2128}\x{212a}-\x{212d}\x{212f}-\x{2139}\x{213c}-\x{213f}\x{2145}-\x{2149}\x{214e}\x{214f}\x{2160}-\x{2188}\x{2336}-\x{237a}\x{2395}\x{249c}-\x{24e9}\x{26ac}\x{2800}-\x{28ff}\x{2c00}-\x{2c2e}\x{2c30}-\x{2c5e}\x{2c60}-\x{2ce4}\x{2ceb}-\x{2cee}\x{2cf2}\x{2cf3}\x{2d00}-\x{2d25}\x{2d27}\x{2d2d}\x{2d30}-\x{2d67}\x{2d6f}\x{2d70}\x{2d80}-\x{2d96}\x{2da0}-\x{2da6}\x{2da8}-\x{2dae}\x{2db0}-\x{2db6}\x{2db8}-\x{2dbe}\x{2dc0}-\x{2dc6}\x{2dc8}-\x{2dce}\x{2dd0}-\x{2dd6}\x{2dd8}-\x{2dde}\x{3005}-\x{3007}\x{3021}-\x{3029}\x{302e}\x{302f}\x{3031}-\x{3035}\x{3038}-\x{303c}\x{3041}-\x{3096}\x{309d}-\x{309f}\x{30a1}-\x{30fa}\x{30fc}-\x{30ff}\x{3105}-\x{312d}\x{3131}-\x{318e}\x{3190}-\x{31ba}\x{31f0}-\x{321c}\x{3220}-\x{324f}\x{3260}-\x{327b}\x{327f}-\x{32b0}\x{32c0}-\x{32cb}\x{32d0}-\x{32fe}\x{3300}-\x{3376}\x{337b}-\x{33dd}\x{33e0}-\x{33fe}\x{3400}-\x{4db5}\x{4e00}-\x{9fd5}\x{a000}-\x{a48c}\x{a4d0}-\x{a60c}\x{a610}-\x{a62b}\x{a640}-\x{a66e}\x{a680}-\x{a69d}\x{a6a0}-\x{a6ef}\x{a6f2}-\x{a6f7}\x{a722}-\x{a787}\x{a789}-\x{a7ad}\x{a7b0}-\x{a7b7}\x{a7f7}-\x{a801}\x{a803}-\x{a805}\x{a807}-\x{a80a}\x{a80c}-\x{a824}\x{a827}\x{a830}-\x{a837}\x{a840}-\x{a873}\x{a880}-\x{a8c3}\x{a8ce}-\x{a8d9}\x{a8f2}-\x{a8fd}\x{a900}-\x{a925}\x{a92e}-\x{a946}\x{a952}\x{a953}\x{a95f}-\x{a97c}\x{a983}-\x{a9b2}\x{a9b4}\x{a9b5}\x{a9ba}\x{a9bb}\x{a9bd}-\x{a9cd}\x{a9cf}-\x{a9d9}\x{a9de}-\x{a9e4}\x{a9e6}-\x{a9fe}\x{aa00}-\x{aa28}\x{aa2f}\x{aa30}\x{aa33}\x{aa34}\x{aa40}-\x{aa42}\x{aa44}-\x{aa4b}\x{aa4d}\x{aa50}-\x{aa59}\x{aa5c}-\x{aa7b}\x{aa7d}-\x{aaaf}\x{aab1}\x{aab5}\x{aab6}\x{aab9}-\x{aabd}\x{aac0}\x{aac2}\x{aadb}-\x{aaeb}\x{aaee}-\x{aaf5}\x{ab01}-\x{ab06}\x{ab09}-\x{ab0e}\x{ab11}-\x{ab16}\x{ab20}-\x{ab26}\x{ab28}-\x{ab2e}\x{ab30}-\x{ab65}\x{ab70}-\x{abe4}\x{abe6}\x{abe7}\x{abe9}-\x{abec}\x{abf0}-\x{abf9}\x{ac00}-\x{d7a3}\x{d7b0}-\x{d7c6}\x{d7cb}-\x{d7fb}\x{e000}-\x{fa6d}\x{fa70}-\x{fad9}\x{fb00}-\x{fb06}\x{fb13}-\x{fb17}\x{ff21}-\x{ff3a}\x{ff41}-\x{ff5a}\x{ff66}-\x{ffbe}\x{ffc2}-\x{ffc7}\x{ffca}-\x{ffcf}\x{ffd2}-\x{ffd7}\x{ffda}-\x{ffdc}\x{10000}-\x{1000b}\x{1000d}-\x{10026}\x{10028}-\x{1003a}\x{1003c}\x{1003d}\x{1003f}-\x{1004d}\x{10050}-\x{1005d}\x{10080}-\x{100fa}\x{10100}\x{10102}\x{10107}-\x{10133}\x{10137}-\x{1013f}\x{101d0}-\x{101fc}\x{10280}-\x{1029c}\x{102a0}-\x{102d0}\x{10300}-\x{10323}\x{10330}-\x{1034a}\x{10350}-\x{10375}\x{10380}-\x{1039d}\x{1039f}-\x{103c3}\x{103c8}-\x{103d5}\x{10400}-\x{1049d}\x{104a0}-\x{104a9}\x{10500}-\x{10527}\x{10530}-\x{10563}\x{1056f}\x{10600}-\x{10736}\x{10740}-\x{10755}\x{10760}-\x{10767}\x{11000}\x{11002}-\x{11037}\x{11047}-\x{1104d}\x{11066}-\x{1106f}\x{11082}-\x{110b2}\x{110b7}\x{110b8}\x{110bb}-\x{110c1}\x{110d0}-\x{110e8}\x{110f0}-\x{110f9}\x{11103}-\x{11126}\x{1112c}\x{11136}-\x{11143}\x{11150}-\x{11172}\x{11174}-\x{11176}\x{11182}-\x{111b5}\x{111bf}-\x{111c9}\x{111cd}\x{111d0}-\x{111df}\x{111e1}-\x{111f4}\x{11200}-\x{11211}\x{11213}-\x{1122e}\x{11232}\x{11233}\x{11235}\x{11238}-\x{1123d}\x{11280}-\x{11286}\x{11288}\x{1128a}-\x{1128d}\x{1128f}-\x{1129d}\x{1129f}-\x{112a9}\x{112b0}-\x{112de}\x{112e0}-\x{112e2}\x{112f0}-\x{112f9}\x{11302}\x{11303}\x{11305}-\x{1130c}\x{1130f}\x{11310}\x{11313}-\x{11328}\x{1132a}-\x{11330}\x{11332}\x{11333}\x{11335}-\x{11339}\x{1133d}-\x{1133f}\x{11341}-\x{11344}\x{11347}\x{11348}\x{1134b}-\x{1134d}\x{11350}\x{11357}\x{1135d}-\x{11363}\x{11480}-\x{114b2}\x{114b9}\x{114bb}-\x{114be}\x{114c1}\x{114c4}-\x{114c7}\x{114d0}-\x{114d9}\x{11580}-\x{115b1}\x{115b8}-\x{115bb}\x{115be}\x{115c1}-\x{115db}\x{11600}-\x{11632}\x{1163b}\x{1163c}\x{1163e}\x{11641}-\x{11644}\x{11650}-\x{11659}\x{11680}-\x{116aa}\x{116ac}\x{116ae}\x{116af}\x{116b6}\x{116c0}-\x{116c9}\x{11700}-\x{11719}\x{11720}\x{11721}\x{11726}\x{11730}-\x{1173f}\x{118a0}-\x{118f2}\x{118ff}\x{11ac0}-\x{11af8}\x{12000}-\x{12399}\x{12400}-\x{1246e}\x{12470}-\x{12474}\x{12480}-\x{12543}\x{13000}-\x{1342e}\x{14400}-\x{14646}\x{16800}-\x{16a38}\x{16a40}-\x{16a5e}\x{16a60}-\x{16a69}\x{16a6e}\x{16a6f}\x{16ad0}-\x{16aed}\x{16af5}\x{16b00}-\x{16b2f}\x{16b37}-\x{16b45}\x{16b50}-\x{16b59}\x{16b5b}-\x{16b61}\x{16b63}-\x{16b77}\x{16b7d}-\x{16b8f}\x{16f00}-\x{16f44}\x{16f50}-\x{16f7e}\x{16f93}-\x{16f9f}\x{1b000}\x{1b001}\x{1bc00}-\x{1bc6a}\x{1bc70}-\x{1bc7c}\x{1bc80}-\x{1bc88}\x{1bc90}-\x{1bc99}\x{1bc9c}\x{1bc9f}\x{1d000}-\x{1d0f5}\x{1d100}-\x{1d126}\x{1d129}-\x{1d166}\x{1d16a}-\x{1d172}\x{1d183}\x{1d184}\x{1d18c}-\x{1d1a9}\x{1d1ae}-\x{1d1e8}\x{1d360}-\x{1d371}\x{1d400}-\x{1d454}\x{1d456}-\x{1d49c}\x{1d49e}\x{1d49f}\x{1d4a2}\x{1d4a5}\x{1d4a6}\x{1d4a9}-\x{1d4ac}\x{1d4ae}-\x{1d4b9}\x{1d4bb}\x{1d4bd}-\x{1d4c3}\x{1d4c5}-\x{1d505}\x{1d507}-\x{1d50a}\x{1d50d}-\x{1d514}\x{1d516}-\x{1d51c}\x{1d51e}-\x{1d539}\x{1d53b}-\x{1d53e}\x{1d540}-\x{1d544}\x{1d546}\x{1d54a}-\x{1d550}\x{1d552}-\x{1d6a5}\x{1d6a8}-\x{1d6da}\x{1d6dc}-\x{1d714}\x{1d716}-\x{1d74e}\x{1d750}-\x{1d788}\x{1d78a}-\x{1d7c2}\x{1d7c4}-\x{1d7cb}\x{1d800}-\x{1d9ff}\x{1da37}-\x{1da3a}\x{1da6d}-\x{1da74}\x{1da76}-\x{1da83}\x{1da85}-\x{1da8b}\x{1f110}-\x{1f12e}\x{1f130}-\x{1f169}\x{1f170}-\x{1f19a}\x{1f1e6}-\x{1f202}\x{1f210}-\x{1f23a}\x{1f240}-\x{1f248}\x{1f250}\x{1f251}\x{20000}-\x{2a6d6}\x{2a700}-\x{2b734}\x{2b740}-\x{2b81d}\x{2b820}-\x{2cea1}\x{2f800}-\x{2fa1d}\x{f0000}-\x{ffffd}\x{100000}-\x{10fffd}]+/u',
		'rtl' => '/[\x{590}\x{5be}\x{5c0}\x{5c3}\x{5c6}\x{5c8}-\x{5ff}\x{7c0}-\x{7ea}\x{7f4}\x{7f5}\x{7fa}-\x{815}\x{81a}\x{824}\x{828}\x{82e}-\x{858}\x{85c}-\x{89f}\x{200f}\x{fb1d}\x{fb1f}-\x{fb28}\x{fb2a}-\x{fb4f}\x{10800}-\x{1091e}\x{10920}-\x{10a00}\x{10a04}\x{10a07}-\x{10a0b}\x{10a10}-\x{10a37}\x{10a3b}-\x{10a3e}\x{10a40}-\x{10ae4}\x{10ae7}-\x{10b38}\x{10b40}-\x{10e5f}\x{10e7f}-\x{10fff}\x{1e800}-\x{1e8cf}\x{1e8d7}-\x{1edff}\x{1ef00}-\x{1efff}\x{608}\x{60b}\x{60d}\x{61b}-\x{64a}\x{66d}-\x{66f}\x{671}-\x{6d5}\x{6e5}\x{6e6}\x{6ee}\x{6ef}\x{6fa}-\x{710}\x{712}-\x{72f}\x{74b}-\x{7a5}\x{7b1}-\x{7bf}\x{8a0}-\x{8e2}\x{fb50}-\x{fd3d}\x{fd40}-\x{fdcf}\x{fdf0}-\x{fdfc}\x{fdfe}\x{fdff}\x{fe70}-\x{fefe}\x{1ee00}-\x{1eeef}\x{1eef2}-\x{1eeff}]+/u',
	);

	/**
	 * Gets the distribution of RTL and LTR characters in the text
	 *
	 * This is the rule the BIDI algorithm uses to determine the directionality of
	 * paragraphs ( http://unicode.org/reports/tr9/#The_Paragraph_Level ) and
	 * FSI isolates ( http://unicode.org/reports/tr9/#Explicit_Directional_Isolates ).
	 *
	 * TODO: Does not handle BIDI control characters inside the text.
	 * TODO: Does not handle unallocated characters.
	 *
	 * @param string $text Text to test
	 * @return array Array with number of ltr characters and rtl characters
	 */
	public static function DirGroupsCount( $text = '' ) {
		$count = array(
			'ltr' => 0,
			'rtl' => 0,
		);
		$offset = array(
			'ltr' => 0,
			'rtl' => 0,
		);
		$match = array(
			'ltr' => '',
			'rtl' => '',
		);

		// We are going to iterate over the string with offsets
		// this is better for memory than using preg_match_all
		// (h/t @mattflaschen for this)
		$dirs = array( 'ltr', 'rtl' );
		foreach ( $dirs as $dir ) {
			// TODO: Consider making this more efficient by
			// 1. Measuring length of text before
			// 2. Deleting (through regexp) all RTL chars
			// 3. Measuring length of text after
			while ( $result = preg_match(
					self::$strongDirRegex[ $dir ],
					$text,
					$match[ $dir ],
					PREG_OFFSET_CAPTURE,
					$offset[ $dir ]
				)
			) {


			    $offset[ $dir ] = $match[ $dir ][0][1] + strlen( $match[ $dir ][0][0]);

				// We should count the length of actual characters rather
				// than bytes (which is what strlen is counting)
				$count[ $dir ] = $count[ $dir ] + mb_strlen( $match[ $dir ][0][0] );
			}
		}

		return $count;
	}

	/**
	 * Get an existence test for character directionality
	 *
	 * @param string $text Text to test
	 * @param int $offset String offset to start the test
	 * @return array Array with boolean values of whether ltr and rtl characters exist
	 */
	public static function DirGroupsExist( $text = '', $offset = 0 ) {
		return array(
			'ltr' => (bool)preg_match( self::$strongDirRegex[ 'ltr' ], $text, $ltr_matches ),
			'rtl' => (bool)preg_match( self::$strongDirRegex[ 'rtl' ], $text,  $rtl_matches ),
		);

	}


}
