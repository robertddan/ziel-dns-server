<?php


$rSocket = socket_create(AF_INET, SOCK_DGRAM, 0);

$aIps = array(
	'kevin' => '127.0.0.1',
	'madcoder' => '127.0.0.2'
);

socket_bind($rSocket, $aIps['kevin']);
socket_connect($rSocket, '127.0.0.1', 80);

#$sRequest = 'GET / HTTP/1.1' . "\r\n" . 'Host: example.com' . "\r\n\r\n";
#$rResponse = socket_write($rSocket, $sRequest);
#var_dump($rResponse);

$from = '';
$port = 0;
$bytes_received = socket_recvfrom($rSocket, $buf, 65536, 0, $from);

white (true) {
	
	#print_r( $bytes_received );
	#print $bytes_received;
	sleep(1);
}
#echo "Received $buf from remote address $from and remote port $port" . PHP_EOL;


// Close
socket_close($rSocket);
?>
