<?php
# @ https://www.askapache.com/pub/php/gethostbyaddr.php
# Copyright (C) 2013 Free Software Foundation, Inc.
#
#   This program is free software: you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation, either version 3 of the License, or
#   (at your option) any later version.
#
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with this program.  If not, see <http://www.gnu.org/licenses/>.

var_dump(gethostbyaddr_timeout('40.85.87.151', '10.3.0.2'));

function gethostbyaddr_timeout($ip, $dns, $timeout=1000)
{
	// random transaction number (for routers etc to get the reply back)
	$data = rand(0, 99);
	// trim it to 2 bytes
	$data = substr($data, 0, 2);
	// request header
	$data .= "\1\0\0\1\0\0\0\0\0\0";
	// split IP up
	$bits = explode(".", $ip);
	// error checking
	if (count($bits) != 4) return "ERROR";
	// there is probably a better way to do this bit...
	// loop through each segment
	for ($x=3; $x>=0; $x--)
	{
		// needs a byte to indicate the length of each segment of the request
		switch (strlen($bits[$x]))
		{
			case 1: // 1 byte long segment
				$data .= "\1"; break;
			case 2: // 2 byte long segment
				$data .= "\2"; break;
			case 3: // 3 byte long segment
				$data .= "\3"; break;
			default: // segment is too big, invalid IP
				return "INVALID";
		}
		// and the segment itself
		$data .= $bits[$x];
	}
	// and the final bit of the request
	$data .= "\7in-addr\4arpa\0\0\x0C\0\1";
	// create UDP socket
	var_dump($data);
	$handle = fsockopen("udp://$dns", 53);
	// send our request (and store request size so we can cheat later)
	$requestsize=fwrite($handle, $data);
	socket_set_timeout($handle, $timeout - $timeout%1000, $timeout%1000);
	var_dump([$data, $handle, $requestsize]);
	
	// hope we get a reply
	$response = fread($handle, 1000);	
	var_dump([$response]);
	
	
	fclose($handle);
	if ($response == "") return $ip;
	// find the response type
	$type = unpack("s", substr($response, $requestsize+2));
	if ($type[1] == 0x0C00)  // answer
	{
		// set up our variables
		$host="";
		$len = 0;
		// set our pointer at the beginning of the hostname
		// uses the request size from earlier rather than work it out
		$position=$requestsize+12;
		// reconstruct hostname
		do
		{
			// get segment size
			$len = unpack("c", substr($response, $position));
			// null terminated string, so length 0 = finished
			if ($len[1] == 0)
			// return the hostname, without the trailing .
			return substr($host, 0, strlen($host) -1);
			// add segment to our host
			$host .= substr($response, $position+1, $len[1]) . ".";
			// move pointer on to the next segment
			$position += $len[1] + 1;
		}
		while ($len != 0);
		// error - return the hostname we constructed (without the . on the end)
		return $ip;
	}
	return $ip;
}


/*

# @ https://www.askapache.com/pub/php/gethostbyaddr.php
# Copyright (C) 2013 Free Software Foundation, Inc.
#
#   This program is free software: you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation, either version 3 of the License, or
#   (at your option) any later version.
#
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with this program.  If not, see <http://www.gnu.org/licenses/>.



function gethostbyaddr_timeout( $ip, $dns, $timeout = 3 ) {
	// based off of http://www.php.net/manual/en/function.gethostbyaddr.php#46869
	// @ https://www.askapache.com/pub/php/gethostbyaddr.php
	// @ https://www.askapache.com/php/php-fsockopen-dns-udp/

	// random transaction number (for routers etc to get the reply back)
	$data = rand( 10, 77 ) . "\1\0\0\1\0\0\0\0\0\0";
	
	// octals in the array, keys are strlen of bit
	$bitso = array("","\1","\2","\3" );
	foreach( array_reverse( explode( '.', $ip ) ) as $bit ) {
		$l=strlen($bit);
		$data.="{$bitso[$l]}".$bit;
	}
	
    // and the final bit of the request
	$data .= "\7in-addr\4arpa\0\0\x0C\0\1";
		
    // create UDP socket
	$errno = $errstr = 0;
    $fp = fsockopen( "udp://{$dns}", 53, $errno, $errstr, $timeout );
	if( ! $fp || ! is_resource( $fp ) )
		return $errno;

	if( function_exists( 'socket_set_timeout' ) ) {
		socket_set_timeout( $fp, $timeout );
	} elseif ( function_exists( 'stream_set_timeout' ) ) {
		stream_set_timeout( $fp, $timeout );
	}


	// send our request (and store request size so we can cheat later)
	$requestsize = fwrite( $fp, $data );
	$max_rx = $requestsize * 3;
	
	$start = time();
	$responsesize = 0;
	while ( $received < $max_rx && ( ( time() - $start ) < $timeout ) && ($buf = fread( $fp, 1 ) ) !== false ) {
		$responsesize++;
		$response .= $buf;
	}
	// echo "[tx: $requestsize bytes]  [rx: {$responsesize} bytes]";

    // hope we get a reply
    if ( is_resource( $fp ) )
		fclose( $fp );

	// if empty response or bad response, return original ip
    if ( empty( $response ) || bin2hex( substr( $response, $requestsize + 2, 2 ) ) != '000c' )
		return $ip;
		
	// set up our variables
	$host = '';
	$len = $loops = 0;
	
	// set our pointer at the beginning of the hostname uses the request size from earlier rather than work it out
	$pos = $requestsize + 12;
	do {
		// get segment size
		$len = unpack( 'c', substr( $response, $pos, 1 ) );
		
		// null terminated string, so length 0 = finished - return the hostname, without the trailing .
		if ( $len[1] == 0 )
			return substr( $host, 0, -1 );
			
		// add segment to our host
		$host .= substr( $response, $pos + 1, $len[1] ) . '.';
		
		// move pointer on to the next segment
		$pos += $len[1] + 1;
		
		// recursion protection
		$loops++;
	}
	while ( $len[1] != 0 && $loops < 20 );
	
	// return the ip in case 
	return $ip;
}


*/
?>

