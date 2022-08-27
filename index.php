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
#var_dump(implode(" ", $aHexBuffer));

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
	switch($k){
		case 0: # tx id
		case 1:
			array_push($aMessage['HEADER']['ID'], base_convert($sField, 2, 16));
		break;
		case 2:
			#1 QR, 4 Opcode, 1 AA, 1 TC, 1 RD
			$aFields = str_split($sField);
			array_push($aMessage['HEADER']['Various']['QR'], base_convert($sField[0], 2, 16));
			array_push($aMessage['HEADER']['Various']['Opcode'], ...array_map(function($sMapField) { return base_convert($sMapField, 2, 10); }, array_slice($aFields, 1, 4)) );
			array_push($aMessage['HEADER']['Various']['AA'], base_convert($aFields[5], 2, 10));
			array_push($aMessage['HEADER']['Various']['TC'], base_convert($aFields[6], 2, 10));
			array_push($aMessage['HEADER']['Various']['RD'], base_convert($aFields[7], 2, 10));
		break;
		case 3:
			# 1 RA, 3 Z, 4 RCODE
			$aFields = str_split($sField);
			array_push($aMessage['HEADER']['Various']['RA'], base_convert($aFields[1], 2, 10));
			array_push($aMessage['HEADER']['Various']['Z'], ...array_map(function($sMapField) { return base_convert($sMapField, 2, 10); }, array_slice($aFields, 1, 3)) );
			array_push($aMessage['HEADER']['Various']['RCODE'], ...array_map(function($sMapField) { return base_convert($sMapField, 2, 10); }, array_slice($aFields, 4, 4)) );
		break;
		case 4: # QDCOUNT
		case 5:
			array_push($aMessage['HEADER']['QDCOUNT'], base_convert($sField, 2, 16));
		break;
		case 6: # ANCOUNT
		case 7:
			array_push($aMessage['HEADER']['ANCOUNT'], base_convert($sField, 2, 16));
		break;
		case 8: # NSCOUNT
		case 9:
			array_push($aMessage['HEADER']['NSCOUNT'], base_convert($sField, 2, 16));
		break;
		case 10: # ARCOUNT
		case 11:
			array_push($aMessage['HEADER']['ARCOUNT'], base_convert($sField, 2, 16));
		break;
	}
}


/*
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
$ikCount = $ikLength = $iCount = 0;
$ik_label = -1;
foreach($aBuffer as $k => $sField)
{
	switch($k){
		case ($k == (12 + $ikCount)): # domain length
			$iCount = (int) base_convert($sField, 2, 10);
			if ($iCount == 0) $ikLength = $ikCount = count($aBuffer) + 1;
			else $ikLength = $ikCount + 1;
			$ik_label = $ik_label + 1;
		break;
		case ($k == (12 + $ikLength)): # domain count
			$iCount = $iCount - 1;
			$ikLength = $ikLength + 1;
			if ($iCount == 0) $ikCount = $ikLength;
			if (!isset($aMessage['QUESTION']['QNAME'][$ik_label])) $aMessage['QUESTION']['QNAME'][$ik_label] = array();
			array_push($aMessage['QUESTION']['QNAME'][$ik_label], chr(base_convert($sField, 2, 10)));
		break;
	}
}



#var_dump(array('$aBuffer', $aBuffer));
#var_dump(implode(" ", $aBuffer));

var_dump($aMessage);
	
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