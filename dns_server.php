<?php




	$rSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	if ($rSocket === false) print_r(socket_strerror(socket_last_error())).PHP_EOL;
	if (!socket_bind($rSocket, "127.0.0.1", 53)) { 
		socket_close($rSocket);
		print_r(socket_strerror(socket_last_error())).PHP_EOL;
	}

while (true){
	socket_recvfrom($rSocket, $buf, 65535, 0, $clientIP, $clientPort);
	
	var_dump($buf);
	sleep(1);
}



/*
- send zone request to get ip address
- get request from client and bind to webserver (apache)

*/
?>