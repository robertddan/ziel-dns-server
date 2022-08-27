<?php

	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	if($socket === false) print_r(socket_strerror(socket_last_error())).PHP_EOL;
	if(!socket_bind($socket, "0.0.0.0", 53)) { 
		socket_close($socket);
		print_r(socket_strerror(socket_last_error())).PHP_EOL;
	}
	socket_recvfrom($socket, $buf, 65535, 0, $clientIP, $clientPort);

	
	$aBuffer = array_map(function($sField) {
		$sField = base_convert(ord($sField), 10, 2);
		$sField = str_pad($sField, 8, 0, STR_PAD_LEFT);
		return $sField;
	}, str_split($buf));
		
	$aHexBuffer = array_map(function($sField) {
		$sField = base_convert(ord($sField), 10, 16);
		$sField = str_pad($sField, 2, 0, STR_PAD_LEFT);
		return $sField;
	}, str_split($buf));

	#var_dump(array('$aBuffer', $aBuffer));
	var_dump(implode(" ", $aHexBuffer));
	
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
                                    1  1  1  1  1  1
      0  1  2  3  4  5  6  7  8  9  0  1  2  3  4  5
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |                                               |
    /                     QNAME                     /
    /                                               /
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |                     QTYPE                     |
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
    |                     QCLASS                    |
    +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
*/

	#$aQSDname = $aQDname = $aQTLDname = array();
	#$sTxId = $sTxId45 = $sTxId67 = $sTxId89 = $sTxIdab = "";
	
	$iDomainCount = $iDomainLength = 0;
	$aMessage = array(
		'HEADER' => array(
			'ID' => array(),
			'Various' => array(
				'QR' => array(),
				'Opcode' => array(),
				'AA' => array(),
				'TC' => array(),
				'RD' => array(),
				'RA' => array(),
				'Z' => array(),
				'RCODE' => array(),
			),
			'QDCOUNT' => array(),
			'ANCOUNT' => array(),
			'NSCOUNT' => array(),
			'ARCOUNT' => array(),
		),
		'QUESTION' => array(
			'QNAME' => array(),
			'QTYPE' => array(),
			'QCLASS' => array(),
		)
	);
	
	/*
	 0 ,  42,                           // HEADER: ID
	 0 ,  0 ,                           // HEADER: Various flags
	 0 ,  1 ,                           // HEADER: QDCOUNT
	 0 ,  0 ,                           // HEADER: ANCOUNT
	 0 ,  0 ,                           // HEADER: NSCOUNT
	 0 ,  0 ,                           // HEADER: ARCOUNT

	 3 , 'w', 'w', 'w',                 // QUESTION: QNAME: label 1
	 6 , 'g', 'o', 'o', 'g', 'l', 'e',  // QUESTION: QNAME: label 2
	 3 , 'c', 'o', 'm',                 // QUESTION: QNAME: label 3
	 
	 0 ,                                // QUESTION: QNAME: null label
	 0 ,  1 ,                           // QUESTION: QTYPE
	 0 ,  1                             // QUESTION: QCLASS
	 */
		

	foreach($aBuffer as $k => $sField)
	{
		switch($k){
			case 0: # tx id
			case 1:
				array_push($aMessage['HEADER']['ID'], base_convert($sField, 2, 16));
				var_dump(['0 1 HEADER:', $aMessage['HEADER']['ID']]);
			break;
			case 2:
				#1 QR, 4 Opcode, 1 AA, 1 TC, 1 RD
				$aFields = str_split($sField);
				array_push($aMessage['HEADER']['Various']['QR'], base_convert($sField[0], 2, 16));
				#var_dump( ...array_map('bindec', array_slice($aFields, 1, 4)) );
				array_push($aMessage['HEADER']['Various']['Opcode'], ...array_map(function($sMapField) { return base_convert($sMapField, 2, 10); }, array_slice($aFields, 1, 4)) );
				array_push($aMessage['HEADER']['Various']['AA'], base_convert($aFields[5], 2, 10));
				array_push($aMessage['HEADER']['Various']['TC'], base_convert($aFields[6], 2, 10));
				array_push($aMessage['HEADER']['Various']['RD'], base_convert($aFields[7], 2, 10));
				var_dump(['2 HEADER:', $aMessage['HEADER']['Various']]);
			break;
			case 3:
				# 1 RA, 3 Z, 4 RCODE
				$aFields = str_split($sField);
				array_push($aMessage['HEADER']['Various']['RA'], base_convert($aFields[1], 2, 10));
				array_push($aMessage['HEADER']['Various']['Z'], ...array_map(function($sMapField) { return base_convert($sMapField, 2, 10); }, array_slice($aFields, 1, 3)) );
				#$aMessage['HEADER']['Various']['Z'] = base_convert(implode(array_slice($aFields, 1, 3)), 2, 10);
				array_push($aMessage['HEADER']['Various']['RCODE'], ...array_map(function($sMapField) { return base_convert($sMapField, 2, 10); }, array_slice($aFields, 4, 4)) );
				#$aMessage['HEADER']['Various']['RCODE'] = base_convert(implode(array_slice($aFields, 4, 4)), 2, 10);
				
				var_dump(['3 HEADER:', $aMessage['HEADER']['Various']]);
			break;
			case 4: # QDCOUNT
			case 5:
				array_push($aMessage['HEADER']['QDCOUNT'], base_convert($sField, 2, 16));
				var_dump(['4 5 QDCOUNT:', $aMessage['HEADER']['QDCOUNT']]);
			break;
			case 6: # ANCOUNT
			case 7:
				array_push($aMessage['HEADER']['ANCOUNT'], base_convert($sField, 2, 16));
				var_dump(['6 7 ANCOUNT:', $aMessage['HEADER']['ANCOUNT']]);
			break;
			case 8: # NSCOUNT
			case 9:
				array_push($aMessage['HEADER']['NSCOUNT'], base_convert($sField, 2, 16));
				var_dump(['8 9 NSCOUNT:', $aMessage['HEADER']['NSCOUNT']]);
			break;
			case 10: # ARCOUNT
			case 11:
				array_push($aMessage['HEADER']['ARCOUNT'], base_convert($sField, 2, 16));
				var_dump(['a b ARCOUNT:', $aMessage['HEADER']['ARCOUNT']]);
			break;
				
				
				
				
			case ($k >= (12 + $iDomainCount) && $k <= (12 + $iDomainLength)): # domain count
				var_dump(['domain count', $sField]);
				die();
				$sQSDcount = base_convert($sField, 2, 10);
				$k_qscn = (int) $sQSDcount;
				$k_qsc = $k + 1;
				$k_qscf = $k_qsc + $k_qscn;
				var_dump(implode(['subdomain count '.$k, " ", $k_qsc, " sQSDcount: ", $sQSDcount, " k_qsc ", $k_qsc, ' $sField: ', $sField]));
			break;
			case ($k >= $k_qsc && $k < $k_qscf): # subdomain name (www)
				if ($k_qsc < $k_qscn) $k_qsc = $k_qsc + 1;
				$k_qsdc = $k + 1;
				$ak_label = 'subdomain'; #count($aMessage['QUESTION']['QNAME']);
				if (!isset($aMessage['QUESTION']['QNAME'][$ak_label])) {
					$aMessage['QUESTION']['QNAME'][$ak_label] = array();
				}
var_dump('$ak_label', $ak_label);
				array_push($aMessage['QUESTION']['QNAME'][$ak_label], chr(base_convert($sField, 2, 10)));
				var_dump(['ak_label', $ak_label, 'QUESTION QNAME:', $aMessage['QUESTION']['QNAME'], ' $sField: ', $sField]);
			break;
				
			case ($k == $k_qsdc): # domain count
				$sQDcount = base_convert($sField, 2, 10);
				$k_qdcn = (int) $sQDcount;
				$k_qdc = $k + 1;
				$k_qdcf = $k_qdc + $k_qdcn;
				var_dump(implode(['domain count '.$k, " ", $k_qdcn, " sQDcount ", $sQDcount]));
			break;
			case ($k >= $k_qdc && $k < $k_qdcf): # domain name (suiteziel)
				if ($k_qsc < $k_qscn) $k_qsc = $k_qsc + 1;
				$k_qdnc = $k + 1;
				$ak_label = 'domain'; #count($aMessage['QUESTION']['QNAME']);
				if (!isset($aMessage['QUESTION']['QNAME'][$ak_label])) {
					$aMessage['QUESTION']['QNAME'][$ak_label] = array();
				}
var_dump('$ak_label', $ak_label);
				if (!isset($aMessage['QUESTION']['QNAME'][$ak_label])) $aMessage['QUESTION']['QNAME'][$ak_label] = array();
				array_push($aMessage['QUESTION']['QNAME'][$ak_label], chr(base_convert($sField, 2, 10)));
				var_dump(['ak_label', $ak_label, 'QUESTION QNAME:', $aMessage['QUESTION']['QNAME'], ' $sField: ', $sField]);
			break;
				
			case ($k == $k_qdnc): # top-level domain count
				$sQTLDcount = base_convert($sField, 2, 10);
				$k_qtldcn = (int) $sQTLDcount;
				$k_qtldc = $k + 1;
				$k_qtldcf = $k_qtldc + $k_qtldcn;
				var_dump(implode(['top-level domain count '.$k, " ", $k_qtldcn, " sQTLDcount ", $sQTLDcount, ' $sField: ', $sField]));
			break;
			case ($k >= $k_qtldc && $k < $k_qtldcf): # top-level domain name (com)
				if ($k_qtldc < $k_qtldcn) $k_qtldc = $k_qtldc + 1;
				$k_qtldnc = $k + 1;
				$ak_label = 'tld'; #count($aMessage['QUESTION']['QNAME']);
				if (!isset($aMessage['QUESTION']['QNAME'][$ak_label])) {
					$aMessage['QUESTION']['QNAME'][$ak_label] = array();
				}
var_dump('$ak_label', $ak_label);
				if (!isset($aMessage['QUESTION']['QNAME'][$ak_label])) $aMessage['QUESTION']['QNAME'][$ak_label] = array();
				array_push($aMessage['QUESTION']['QNAME'][$ak_label], chr(base_convert($sField, 2, 10)));
				var_dump(['ak_label', $ak_label, 'QUESTION QNAME:', $aMessage['QUESTION']['QNAME'], ' $sField: ', $sField]);
			break;
			case ($k == $k_qtldcf): # End domain
				$k_qtype= $k_qtldcf + 1;
				var_dump(['End domain:'. $k . " ", base_convert($sField, 2, 16), ' $sField: ', $sField]);
			break;
				
				
				
				
			case ($k == $k_qtype):  # QTYPE
			case ($k == ($k_qtype + 1)):
				$k_qclass = $k_qtype + 2;
				array_push($aMessage['QUESTION']['QTYPE'], base_convert($sField, 2, 16));
				var_dump(['QUESTION QTYPE:'. $k . " ", $aMessage['QUESTION']['QTYPE'], ' $sField: ', $sField]);
			break;
				
			case ($k == $k_qclass): # QCLASS
			case ($k == ($k_qclass + 1)): 
				array_push($aMessage['QUESTION']['QCLASS'], base_convert($sField, 2, 16));
				var_dump(['QUESTION QCLASS:'. $k . " ", $aMessage['QUESTION']['QCLASS'], ' $sField: ', $sField]);
			break;
		}
		
		var_dump(implode(array($k.' $sField: ', $sField)));
	}
	#var_dump(array('$aBuffer', $aBuffer));
	var_dump(implode(" ", $aBuffer));

	
/*
string(220) "228::210::1::32::0::1::0::0::0::0::0::1::3::119::119::119::9::115::117::105::116::101::122::105::101::108::3::99::111::109::0::0::1::0::1::0::0::41::16::0::0::
0::0::0::0::12::0::10::0::8::84::38::6::218::191::16::25::114"

11100100 11010010 00000001 00100000 00000000 00000001 00000000 00000000 00000000 00000000 00000000 00000001 00000011 01110111 01110111 01110111 00001001 011100
11 01110101 01101001 01110100 01100101 01111010 01101001 01100101 01101100 00000011 01100011 01101111 01101101 00000000 00000000 00000001 00000000 00000001 00000000 0000000
0 00101001 00010000 00000000 00000000 00000000 00000000 00000000 00000000 00001100 00000000 00001010 00000000 00001000 01010100 00100110 00000110 11011010 10111111 00010000
 00011001 01110010
 
*/
	

#socket_send($socket, $ret, 667, 0);

?>