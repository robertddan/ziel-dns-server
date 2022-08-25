<?php

while(true) {
	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	if($socket === false) print_r(socket_strerror(socket_last_error())).PHP_EOL;
	if(!socket_bind($socket, "0.0.0.0", 53)) { 
		socket_close($socket);
		print_r(socket_strerror(socket_last_error())).PHP_EOL;
	}
	socket_recvfrom($socket, $buf, 65535, 0, $clientIP, $clientPort);

	$aBuffer = str_split($buf);
	$aData = array();

	var_dump([$buf, count($aBuffer)]);
	
	foreach($aBuffer as $k => $sField)
	{
		$sField = base_convert(ord($sField), 10, 2);
		$sField = str_pad($sField, 8, 0, STR_PAD_LEFT);
		array_push($aData, $sField);
var_dump($k);
		switch($k){
			case 0: # tx id
			case 1:
				if (!isset($sTxId)) $sTxId = "";
				$sTxId .= base_convert($sField, 2, 16);
				var_dump(['tx id:', $sTxId]);
			break;
			case 2:
				$aFields = str_split($sField);
				#1 QR, 4 Opcode, 1 AA, 1 TC, 1 RD
				$aQuery = array(
					'QR' => base_convert($aFields[0], 2, 16),
					'Opcode' => base_convert(implode(array_slice($aFields, 1, 4)), 2, 16),
					'AA' => base_convert($aFields[5], 2, 16),
					'TC' => base_convert($aFields[6], 2, 16),
					'RD' => base_convert($aFields[7], 2, 16)
				);
				var_dump(['2 query:', $aQuery]);
			break;
			case 2:
				echo "i equals 2";
			break;
		}
	}
	
	var_dump(implode(" ", $aData));

# Get the flags
/*
	var_dump($a) .PHP_EOL;
	$stz = bin2hex($buf);
	$tx = "";
	
	for($i=0;$i<(strlen($stz)-26-10)/2;$i++)
	{
		$e = "00";
		$e[0] = $stz[$i*2+26];
		$e[1] = $stz[$i*2+27];
		$f = hexdec($e);
		if($f > 0 && $f < 32) $tx .= "."; else
		$tx .= sprintf("%c",$f);
	}
	
	echo "$clientIP <".$tx.">\n";
*/
	
}
socket_send($socket,$ret,667,0);

?>