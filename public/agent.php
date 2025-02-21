<?php

namespace Ziel;

class Agent
{
    public static $sock;

    public static function agent_init()
    {
        self::agent_native();
        #self::agent_basic();
        return true;
    }

    public static function agent_native()
    {
        $address = "127.0.0.1";
        $port = 44321;

        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($sock, $address, $port);
        socket_listen($sock);
        $members = [];
        $connections = [];
        $connections[] = $sock;

        echo "Listening for new connections on port $port: " . "\n";

        while (true) {
            $reads = $writes = $exceptions = $connections;
            socket_select($reads, $writes, $exceptions, 0);

            if (in_array($sock, $reads)) {
                $new_connection = socket_accept($sock);
                $header = socket_read($new_connection, 1024);
                self::handshake($header, $new_connection, $address, $port);
                $connections[] = $new_connection;
                $reply = [
                    "type" => "join",
                    "sender" => "Server",
                    #"text" => "enter name to join... \n"
                    "text" => date("Y-m-d H:i:s"),
                ];
                $reply = self::pack_data(json_encode($reply));
                socket_write($new_connection, $reply, strlen($reply));

                $firstIndex = array_search($sock, $reads);
                unset($reads[$firstIndex]);
            }

            foreach ($reads as $key => $value) {
                $data = socket_read($value, 1024);

                if (!empty($data)) {
                    $message = self::unmask($data);
                    $decoded_message = json_decode($message, true);

                    var_dump(['$message', $decoded_message]);
                    
                    #get cookie token for user array
                    if (isset($decoded_message['menu']))
                    switch ($decoded_message['menu']) {
                        case 'new':
                            
                        break;
                    }

                    $decoded_message["text"] = date("Y-m-d H:i:s");
                    $decoded_message["type"] = "join";
                    $message = json_encode($decoded_message);
                    
                    if ($decoded_message) {
                        if (isset($decoded_message["text"])) {
                            if ($decoded_message["type"] === "join") {
                                $members[$key] = [
                                    "name" => "sender", #$decoded_message['sender'],
                                    "connection" => $value,
                                ];
                            }
                            var_dump($message);
                            $maskedMessage = self::pack_data($message);
                            foreach ($members as $mkey => $mvalue) {
                                socket_write(
                                    $mvalue["connection"],
                                    $maskedMessage,
                                    strlen($maskedMessage)
                                );
                            }
                        }
                    }
                } elseif ($data === "") {
                    echo "disconnected " . $key . " \n";
                    unset($connections[$key]);
                    if (array_key_exists($key, $members)) {
                        $message = [
                            "type" => "left",
                            "sender" => "Server",
                            "text" =>
                                $members[$key]["name"] . " left the chat \n",
                        ];
                        $maskedMessage = self::pack_data(json_encode($message));
                        unset($members[$key]);
                        foreach ($members as $mkey => $mvalue) {
                            socket_write(
                                $mvalue["connection"],
                                $maskedMessage,
                                strlen($maskedMessage)
                            );
                        }
                    }
                    socket_close($value);
                }
            }
        }

        socket_close($sock);
    }

    public static function unmask($text)
    {
        $length = ord($text[1]) & 127;
        if ($length == 126) {
            $masks = substr($text, 4, 4);
            $data = substr($text, 8);
        } elseif ($length == 127) {
            $masks = substr($text, 10, 4);
            $data = substr($text, 14);
        } else {
            $masks = substr($text, 2, 4);
            $data = substr($text, 6);
        }
        $text = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i % 4];
        }
        return $text;
    }

    public static function pack_data($text)
    {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);
        if ($length <= 125) 
        $header = pack("CC", $b1, $length);
        elseif ($length > 125 && $length < 65536) 
        $header = pack("CCn", $b1, 126, $length);
        elseif ($length >= 65536)
        $header = pack("CCNN", $b1, 127, $length);
        return $header . $text;
    }

    public static function handshake($request_header, $sock, $host_name, $port)
    {
        $headers = [];
        $lines = preg_split("/\r\n/", $request_header);
        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match("/\A(\S+): (.*)\z/", $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }

        $sec_key = $headers["Sec-WebSocket-Key"];
        $sec_accept = base64_encode(
            pack("H*", sha1($sec_key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"))
        );
        $response_header =
            "HTTP/1.1 101 Switching Protocols\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept:$sec_accept\r\n\r\n";
        socket_write($sock, $response_header, strlen($response_header));
    }
    
    /*
    //self signed certificate
    
    $ssl = [
    'ssl' => [
        'local_cert'  => $path . 'cert.pem',       // SSL Certificate
        'local_pk'    => $path . 'privkey.pem',    // SSL Keyfile
        'disable_compression' => true,             // TLS compression attack vulnerability
        'verify_peer'         => false,            // Set this to true if acting as an SSL client
        'ssltransport' => $transport,              // Transport Methods such as 'tlsv1.1', tlsv1.2' 
    ]];
    
    $stream_context = stream_context_create([
    'ssl' => [
        'local_cert'        => '/path/to/key.pem',
        'peer_fingerprint'  => openssl_x509_fingerprint(file_get_contents('/path/to/key.crt')),
        'verify_peer'       => false,
        'verify_peer_name'  => false,
        'allow_self_signed' => true,
        'verify_depth'      => 0 
    ]]);
    */
}

if (php_sapi_name() == "cli") {
    return Agent::agent_init();
}

?>