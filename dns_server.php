<?php

$sIp = "0.0.0.0";
$iPort = 53;


$rSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($rSocket === false) print_r(socket_strerror(socket_last_error())).PHP_EOL;
if (!socket_bind($rSocket, $sIp, $iPort)) { 
	socket_close($rSocket);
	print_r(socket_strerror(socket_last_error())).PHP_EOL;
}

#for ($i = 0; $i <= 5; $i++){
$i = 0;
while(true) {
	socket_recvfrom($rSocket, $buf, 65535, 0, $sIp, $iPort);
	var_dump($buf);
	
	$sMsg = $i;
	$iLen = strlen($i);
	socket_sendto($rSocket, $sMsg, $iLen, 0, $sIp, $iPort); 
	sleep(1);

	$i = $i + 1;
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