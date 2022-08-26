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
	$aQname = array();
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
				 
				if (!isset($sQcount)) $sQcount = "";
				$sQcount = base_convert($sField, 2, 10);
				$k_q = (int) $sQcount;
				$k_qn = 1;
				
				var_dump(['$k_q '.$k, implode([$k_q]), $k_qn]);
				/*
				if (!isset($sTxIdab)) $sTxIdab = "";
				$sTxIdab .= base_convert($sField, 2, 16);
				var_dump(['a b ARCOUNT:', $sTxIdab]);
				
				
				if (!isset($sMainFields)) $sMainFields = "";
				$sMainFields .= $sField ." ";
				
				var_dump(['$sMainFields '.$k, $sMainFields]);
				
				if (!isset($sTxIdc)) $sQnameC = $sQnameD = "";
				
				$aQname = str_split($sField, 4);
				$sQnameC = base_convert($aQname[0], 2, 16);
				$sQnameD = base_convert($aQname[1], 2, 16);
				
				var_dump(['$sQnameC:', $sQnameC, '$sQnameD', $sQnameD]);
				*/
				
			break;
			case (12 + $k_qn): # subdomain name (www)
				if ($k_qn < $k_q) $k_qn = $k_qn + 1;
				
				if (!isset($sQname)) $sQname = "";
				$aQname[] = chr(base_convert($sField, 2, 10));
				$sQname = implode($aQname);
				
				var_dump(implode(['$k_qn '.$k, " ", $k_qn, " sField: ", $sField, " k_q ", $k_q]));
				var_dump(implode(['$sQname '.$k, " sQname ", $sQname, " k_q ", $k_q]));
			break;
			case ($k > (12 + $k_q)): # domain count
		
				var_dump(implode(['domain count '.$k, " ", $k_qn, " k_q ", $k_q]));
			break;
			case ($k > (12 + $k_q)): # domain name
				var_dump(implode(['domain name '.$k, " ", $k_qn, " k_q ", $k_q]));
			break;
		}
		
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