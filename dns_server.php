<?php

$tx = "";


$stz = "ed4f0120000100000000000106696d6167657306676f6f676c6503636f6d0000010001000029100000000000000c000a00088ff1b12cbe8f026b";

for($i=0;$i<(strlen($stz)-26-10)/2;$i++)
{
	$e = "00";
	$e[0] = $stz[$i*2+26];
	$e[1] = $stz[$i*2+27];
	$f = hexdec($e);
	if($f > 0 && $f < 32) $tx .= "."; else
	$tx .= sprintf("%c",$f);
}
echo "<".$tx.">\n";  


#$fp = fsockopen("udp://72.174.110.4", 53, $errno, $errstr);
if (!$fp)
{
	echo "ERROR: $errno - $errstr<br />\n";
}
else
{
	fwrite($fp, $buf);
	$ret = $buf;
	$ret = fread($fp, 667);
	fclose($fp);
}


var_dump($ret);

socket_send($socket, $ret,667,0);

/*
- send zone request to get ip address
- get request from client and bind to webserver (apache)

*/
?>