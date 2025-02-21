<?php


$a = dns_get_record('suiteziel.com');

var_dump($a);
die();

$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_connect($sock, "8.8.8.8", 53);
$name = '40.85.87.151';
socket_getsockname($sock, $name); // $name passed by reference

// This is the local machine's external IP address
$localAddr = $name;


?>