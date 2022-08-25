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
# Get the flags
/*
                                    1  1  1  1  1  1
      0  1  2  3  4  5  6  7  8  9  0  1  2  3  4  5
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |                      ID                       |
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |QR|   Opcode  |AA|TC|RD|RA|   Z    |   RCODE   |
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |                    QDCOUNT                    |
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |                    ANCOUNT                    |
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |                    NSCOUNT                    |
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |                    ARCOUNT                    |
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
*/
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
				array_pop($aBuffer);
			break;
			case 2:
				#1 QR, 4 Opcode, 1 AA, 1 TC, 1 RD
				$aFields = str_split($sField);
				$aQuery = array(
					'QR' => base_convert($aFields[0], 2, 10),
					'Opcode' => base_convert(implode(array_slice($aFields, 1, 4)), 2, 10),
					'AA' => base_convert($aFields[5], 2, 10),
					'TC' => base_convert($aFields[6], 2, 10),
					'RD' => base_convert($aFields[7], 2, 10)
				);
				var_dump(['2 query:', $aQuery]);
				array_pop($aBuffer);
			break;
			case 3:
				# 1 RA, 3 Z, 4 RCODE
				$aFields = str_split($sField);
				$aQuery = array(
					'RA' => base_convert($aFields[1], 2, 10),
					'Z' => base_convert(implode(array_slice($aFields, 1, 3)), 2, 10),
					'RCODE' => base_convert(implode(array_slice($aFields, 4, 4)), 2, 10),
				);
				var_dump(['3 query:', $aQuery]);
				array_pop($aBuffer);
			break;
			case 4: # QDCOUNT
			case 5:
				if (!isset($sTxId45)) $sTxId45 = "";
				$sTxId45 .= base_convert($sField, 2, 16);
				var_dump(['4 5 QDCOUNT:', $sTxId45]);
				array_pop($aBuffer);
			break;
			case 6: # ANCOUNT
			case 7:
				if (!isset($sTxId67)) $sTxId67 = "";
				$sTxId67 .= base_convert($sField, 2, 16);
				var_dump(['6 7 ANCOUNT:', $sTxId67]);
				array_pop($aBuffer);
			break;
			case 8: # NSCOUNT
			case 9:
				if (!isset($sTxId89)) $sTxId89 = "";
				$sTxId89 .= base_convert($sField, 2, 16);
				var_dump(['8 9 NSCOUNT:', $sTxId89]);
				array_pop($aBuffer);
			break;
			case 10: # ARCOUNT
			case 11:
				if (!isset($sTxIdab)) $sTxIdab = "";
				$sTxIdab .= base_convert($sField, 2, 16);
				var_dump(['a b ARCOUNT:', $sTxIdab]);
				array_pop($aBuffer);
			break;
			case 12: # question
			case 13:
				if (!isset($sTxIdcd)) $sTxIdcd = "";
				$sTxIdcd .= base_convert($sField, 2, 16);
				var_dump(['c d QNAME:', $sTxIdcd]);
				array_pop($aBuffer);
			break;
		}
	}
	var_dump(array('$aBuffer', $aBuffer));
	var_dump(implode(" ", $aData));


	
}
socket_send($socket,$ret,667,0);

?>