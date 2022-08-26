<?php

while(true) {
	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	if($socket === false) print_r(socket_strerror(socket_last_error())).PHP_EOL;
	if(!socket_bind($socket, "0.0.0.0", 53)) { 
		socket_close($socket);
		print_r(socket_strerror(socket_last_error())).PHP_EOL;
	}
	socket_recvfrom($socket, $buf, 65535, 0, $clientIP, $clientPort);

	var_dump($buf);
	$aBuffer = array_map(function($sField) {
		$sField = base_convert(ord($sField), 10, 2);
		$sField = str_pad($sField, 8, 0, STR_PAD_LEFT);
		return $sField; #array_push($aData, $sField);
		#$aData = array();
	}, str_split($buf));
	
	#var_dump(array('$aBuffer', $aBuffer));
	var_dump(implode(" ", $aBuffer));
	
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
	$aQSDname = $aQDname = array();
	$sTxId = "";
	foreach($aBuffer as $k => $sField)
	{
		$aaTx[$k] = base_convert($sField, 2, 10);
		#var_dump(implode('::', $aaTx));

		switch($k){
			case 0: # tx id
			case 1:
				if (!isset($sTxId)) $sTxId = "";
				$sTxId .= base_convert($sField, 2, 16);
				var_dump(['tx id:', $sTxId]);
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
			break;
			case 4: # QDCOUNT
			case 5:
				if (!isset($sTxId45)) $sTxId45 = "";
				$sTxId45 .= base_convert($sField, 2, 16);
				var_dump(['4 5 QDCOUNT:', $sTxId45]);
			break;
			case 6: # ANCOUNT
			case 7:
				if (!isset($sTxId67)) $sTxId67 = "";
				$sTxId67 .= base_convert($sField, 2, 16);
				var_dump(['6 7 ANCOUNT:', $sTxId67]);
			break;
			case 8: # NSCOUNT
			case 9:
				if (!isset($sTxId89)) $sTxId89 = "";
				$sTxId89 .= base_convert($sField, 2, 16);
				var_dump(['8 9 NSCOUNT:', $sTxId89]);
			break;
			case 10: # ARCOUNT
			case 11:
				if (!isset($sTxIdab)) $sTxIdab = "";
				$sTxIdab .= base_convert($sField, 2, 16);
				var_dump(['a b ARCOUNT:', $sTxIdab]);
			break;
			case 12: # subdomain count
				$sQSDcount = base_convert($sField, 2, 10);
				$k_qscn = (int) $sQSDcount;
				$k_qsc = 1;
				
				var_dump(implode(['subdomain count '.$k, " ", $k_qsc, " sQSDcount: ", $sQSDcount, " k_qsc ", $k_qsc]));
			break;
			case (12 + $k_qsc): # subdomain name (www)
				if ($k_qsc < $k_qscn) $k_qsc = $k_qsc + 1;
				#if (!isset($sQSDname)) $sQSDname = "";
				$aQSDname[] = chr(base_convert($sField, 2, 10));
				$sQSDname = implode($aQSDname);
				$k_qsdc = $k + 1;
				
				var_dump(implode(['subdomain name'.$k, " k_qdc ", $k_qsdc, " sQSDname: ", $sQSDname, " k_qsc ", $k_qsc]));
			break;
			case ($k == $k_qsdc): # domain count
				$sQDcount = base_convert($sField, 2, 10);
				$k_qdcn = (int) $sQDcount;
				$k_qdc = $k + 1;
				$k_qdcf = $k_qdc + $sQDcount;
				var_dump(implode(['domain count '.$k, " ", $k_qdcn, " sQDcount ", $sQDcount]));
			break;
			case ($k >= $k_qdc && $k < $k_qdcf): # domain name (suiteziel)
				if ($k_qsc < $k_qscn) $k_qsc = $k_qsc + 1;
				#if (!isset($sQname)) $sQSname = "";
				$aQDname[] = chr(base_convert($sField, 2, 10));
				$sQDname = implode($aQDname);
				$k_qdnc = $k + 1;
				
				var_dump(implode(['subdomain name'.$k, " k_qdc ", $k_qdc, " sQDname: ", $sQDname, " k_qsc ", $k_qsc]));
			break;
			case ($k == $k_qdnc): # top-level domain count
				$sQTLDcount = base_convert($sField, 2, 10);
				$k_qtldcn = (int) $sQTLDcount;
				#$k_qtldc = $k + 1;
				#$k_qtldcf = $k_qtldc + $sQTLDcount;
				var_dump(implode(['top-level domain count '.$k, " ", $k_qtldcn, " sQTLDcount ", $sQTLDcount]));
			break;
			case ($k >= $k_qdc && $k < $k_qdcf): # top-level domain name (com)
				if ($k_qsc < $k_qscn) $k_qsc = $k_qsc + 1;
				#if (!isset($sQname)) $sQSname = "";
				$aQDname[] = chr(base_convert($sField, 2, 10));
				$sQDname = implode($aQDname);
				$k_qdnc = $k + 1;
				
				var_dump(implode(['subdomain name'.$k, " k_qdc ", $k_qdc, " sQDname: ", $sQDname, " k_qsc ", $k_qsc]));
			break;
		}
		
		var_dump(implode(array($k.' $sField: ', $sField)));
	}
	#var_dump(array('$aBuffer', $aBuffer));
	var_dump(implode(" ", $aBuffer));

/*
string(220) "228::210::1::32::0::1::0::0::0::0::0::1::3::119::119::119::9::115::117::105::116::101::122::105::101::108::3::99::111::109::0::0::1::0::1::0::0::41::16::0::0::
0::0::0::0::12::0::10::0::8::84::38::6::218::191::16::25::114"
string(521) "11100100 11010010 00000001 00100000 00000000 00000001 00000000 00000000 00000000 00000000 00000000 00000001 00000011 01110111 01110111 01110111 00001001 011100
11 01110101 01101001 01110100 01100101 01111010 01101001 01100101 01101100 00000011 01100011 01101111 01101101 00000000 00000000 00000001 00000000 00000001 00000000 0000000
0 00101001 00010000 00000000 00000000 00000000 00000000 00000000 00000000 00001100 00000000 00001010 00000000 00001000 01010100 00100110 00000110 11011010 10111111 00010000
 00011001 01110010"
*/
	
}
socket_send($socket,$ret,667,0);

?>