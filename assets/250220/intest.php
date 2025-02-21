<?php

while(true) {
	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	if($socket === false) print_r(socket_strerror(socket_last_error())).PHP_EOL;
	if(!socket_bind($socket, "0.0.0.0", 53)) { 
		socket_close($socket);
		print_r(socket_strerror(socket_last_error())).PHP_EOL;
	}
}
socket_close($socket);


?>