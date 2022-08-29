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
	var_dump(implode(" ", ['$sMsgReceive_in_server ', $sMsgReceive]));
	var_dump(implode(" ", ['connection_in_server_from: ', $sIp, $iPort]));
	
	$sMsg = 'server_'.$i." ".date('Y-m-d H:i:s');
	$iLen = strlen($sMsg);
	socket_sendto($rSocket, $sMsg, $iLen, 0, $sIp, $iPort);
	var_dump(implode(" ", ['$sMsg_send_to_client ', $sMsg]));

	$i = $i + 1;
	sleep(3);
}


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