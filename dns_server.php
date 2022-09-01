<?php

$sZIp = "0.0.0.0";
$iZPort = 53;

$rSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($rSocket === false) print_r(socket_strerror(socket_last_error())).PHP_EOL;
if (!socket_bind($rSocket, $sZIp, $iZPort)) { 
	socket_close($rSocket);
	print_r(socket_strerror(socket_last_error())).PHP_EOL;
}

$i = 0;
while(true) {
	$sIp = '';
	$iPort = 0;
	socket_recvfrom($rSocket, $sMsgReceive, 65535, 0, $sIp, $iPort);
	$a_pid = pcntl_fork();
	var_dump(implode(" ", [' _pid_ ', $a_pid]));
	var_dump(implode(" ", ['$sMsgReceive_in_server ', $sMsgReceive, " _pid_ ", $a_pid]));
	var_dump(implode(" ", ['connection_in_server_from: ', $sIp, $iPort]));
	
	$sMsg = 'server_'.$i." ".date('Y-m-d H:i:s');
	$iLen = strlen($sMsg);
	socket_sendto($rSocket, $sMsg, $iLen, 0, $sIp, $iPort);
	var_dump(implode(" ", ['$sMsg_send_to_client ', $sMsg]));

	$i = $i + 1;
	sleep(3);
}

/*
Field Sub-field Value Intrepretation
ID xdb Response should have ID xdb
Flags x
QR  It’s a query
OPCODE  Standard query
TC  Not truncated
RD  Recursion requested
RA  Not meaningful for query
Z  Reserved
RCODE  Not meaningful for query
QDCOUNT x One question follows
ANCOUNT x No answers follow
NSCOUNT x No records follow
ARCOUNT x No additional records follow

$socket = stream_socket_server("udp://127.0.0.1:1113", $errno, $errstr, STREAM_SERVER_BIND);
if (!$socket) {
    die("$errstr ($errno)");
}

do {
    $pkt = stream_socket_recvfrom($socket, 1, 0, $peer);
    echo "$peer\n";
    stream_socket_sendto($socket, date("D M j H:i:s Y\r\n"), 0, $peer);
} while ($pkt !== false);


*/
#$sMsg = "Ping !";
#$iLen = strlen($msg);

#$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
#socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
#socket_sendto($rSocket, $sMsg, $iLen, 0, $sIp, $iPort); 

socket_close($rSocket);

/*
- send zone request to get ip address
- get request from client and bind to webserver (apache)
*/
?>