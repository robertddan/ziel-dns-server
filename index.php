<?php


$rSocket = socket_create(AF_INET, SOCK_DGRAM, 0);

$aIps = array(
	'kevin' => '127.0.0.1',
	'madcoder' => '127.0.0.2'
);

socket_bind($rSocket, $aIps['madcoder']);
socket_connect($rSocket, '127.0.0.1', 80);

$sRequest = 'GET / HTTP/1.1' . "\r\n" . 'Host: example.com' . "\r\n\r\n";
$rResponse = socket_write($rSocket, $sRequest);


var_dump($rResponse);


$from = '';
$port = 0;
socket_recvfrom($rSocket, $buf, 12, 0, $from, $port);

echo "Received $buf from remote address $from and remote port $port" . PHP_EOL;



$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($socket, '127.0.0.1', 1223);

$from = '';
$port = 0;
socket_recvfrom($socket, $buf, 12, 0, $from, $port);

echo "Received $buf from remote address $from and remote port $port" . PHP_EOL;


// Close
socket_close($rSocket);
?>
