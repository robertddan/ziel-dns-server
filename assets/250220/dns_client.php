<?php

$sIp = "127.0.0.1";
$iPort = 53;

$rSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($rSocket, SOL_SOCKET, SO_BROADCAST, 1);
socket_connect($rSocket, $sIp, $iPort);

$i = 0;
while(true){
	$sMsg = 'client_'.$i." ".date('Y-m-d H:i:s');
	$iLen = strlen($sMsg);
	socket_sendto($rSocket, $sMsg, $iLen, 0, $sIp, $iPort);
	var_dump(implode(" ", ['$sMsg_send_to_server ', $sMsg]));
	
	socket_recvfrom($rSocket, $sMsgReceive, $iLen, 0, $sIp, $iPort);
	var_dump(implode(" ", ['$sMsgReceive_in_client', $sMsgReceive]));
	
	$i = $i + 1;
	sleep(2);
}


socket_close($rSocket);

/*
$package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
$rSocket  = socket_create(AF_INET, SOCK_RAW, 1);
socket_set_option($rSocket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
socket_connect($socket, $host, null);

$ts = microtime(true);
socket_send($socket, $package, strLen($package), 0);
if (socket_read($socket, 255)) $result = microtime(true) - $ts;
else $result = false;
socket_close($socket);

return $result;
*/

/*
- send zone request to get ip address
- get request from client and bind to webserver (apache)

*/


/*
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


	$buf = hex2bin($stz);
	$fp = fsockopen("udp://127.0.0.1", 53, $errno, $errstr);
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



while(true){
	socket_send($socket, $ret, 667, 0);
	var_dump($ret);
	sleep(1);
}
*/
?>