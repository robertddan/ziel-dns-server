<?php

while(true) {
	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	if($socket === false) print_r(socket_strerror(socket_last_error())).PHP_EOL;
	if(!socket_bind($socket, "0.0.0.0", 53)) { 
		socket_close($socket);
		print_r(socket_strerror(socket_last_error())).PHP_EOL;
	}
	socket_recvfrom($socket, $buf, 65535, 0, $clientIP, $clientPort);
   $stz = bin2hex($buf);
   $tx = "";
   for($i=0;$i<(strlen($stz)-26-10)/2;$i++)
   {
     $e = "00";
     $e[0] = $stz[$i*2+26];
     $e[1] = $stz[$i*2+27];
     $f = hexdec($e);
     if($f > 0 && $f < 32) $tx .= "."; else
     $tx .= sprintf("%c",$f);
   }
   echo "$clientIP <".$tx.">\n";   
}
socket_send($socket,$ret,667,0);

?>