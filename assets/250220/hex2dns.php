<?php

$sHexResponse = "dc b0 81 80 00 01 00 01 00 00 00 00 06 67 6f 6f 67 6c 65 03 63 6f 6d 00 00 01 00 01 c0 0c 00 01 00 01 00 00 01 2c 00 04 8e fa 4a 6e";
#$sHexResponse = "DC B0 3C 38 31 3E 3C 38 30 3E 5E 40 5E 41 5E 40 5E 41 5E 40 5E 40 5E 40 5E 40 5E 46 67 6F 6F 67 6C 65 5E 43 63 6F 6D 5E 40 5E 40 5E 41 5E 40 5E 41 C0 5E 4C 5E 40 5E 41 5E 40 5E 41 5E 40 5E 40 5E 41 2C 5E 40 5E 44 3C 38 65 3E FA 4A 6E";

$aHex = explode(" ", $sHexResponse);

foreach($aHex as $sHex) {
	var_dump(array(
		"hexdec", hexdec($sHex), 
		"chr", chr(hexdec($sHex))
	));
}


?>