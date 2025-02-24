<?php

define('DS', DIRECTORY_SEPARATOR);
chdir(__DIR__ . DS . '..' . DS);
define('ROOT', getcwd() . DS);
define('CONFIG', ROOT . 'config' . DS);
define('VENDOR', ROOT . 'vendor' . DS);

require(CONFIG . DS . 'bootstrap.php');

function router_redirect($aRoute = array())
{
    $aRouter = array();
    if (!empty($aRoute)) $aRouter = $aRoute;
    if (empty($aRouter['page'])) $aRouter['page'] = 'home';
    header('Location: /index?'. http_build_query($aRouter));
    exit();
}

global $aRouter, $aWidget, $aPage;

$aRouter = array();
if(empty($_GET)) router_redirect();
$aRouter = parse_url('/'. $_SERVER["REQUEST_URI"]);
parse_str($aRouter["query"], $aQuery);
$aRouter = array_merge($aQuery, $aRouter);
$sNamespace = 'Ziel\View\\'. ucfirst($aRouter['page']);
if (!class_exists($sNamespace)) router_redirect(array('page' => 'error'));
if (!call_user_func(array($sNamespace, $aRouter['page'] .'_init')))
throw_exception('call_user_func()');

var_dump([ROOT .'public'. DS .'favicon.ico', file_exists(ROOT .'public'. DS .'favicon.ico')]);
if (isset($aRouter['host']))
switch ($aRouter['host']) {
    case 'favicon.ico':
        header('Content-Type: text/x-icon');
        print file_get_contents(ROOT .'public'. DS .'favicon.ico');
        exit();
    break;
    case 'app.webmanifest':
        header('Content-Type: application/manifest+json');
        print file_get_contents(ROOT .'public'. DS .'app.webmanifest');
        exit();
    break;
    case 'fonts':
        header('Content-Type: font/ttf; charset=utf-8');
        print file_get_contents(DRAFT .'static'. DS .$aRouter['path']); 
        exit();
    break;
    case 'style':
        header('Content-Type: text/css; charset=utf-8');
        print file_get_contents(DRAFT .'static'. DS .$aRouter['path']);
        exit();
    break;
    case 'script':
        header('Content-Type: text/javascript; charset=utf-8');
        print file_get_contents(DRAFT .'static'. DS .$aRouter['path']);
        exit();
    break;
}


$aWidget['html'] = '';
$aWidget['html'] .= '<!--- html -->';
$aWidget['html'] .= '<html class="">';
$aWidget['html'] .= '<!--- head -->';
$aWidget['html'] .= '<head>';
$aWidget['html'] .= '<meta charset="utf-8">';
$aWidget['html'] .= $aPage['stylesheet'] .PHP_EOL;
$aWidget['html'] .= $aPage['javascript'] .PHP_EOL;
$aWidget['html'] .= '<style>.loader { border: 16px solid #f3f3f3; /* Light grey */ border-top: 16px solid #3498db; /* Blue */ border-radius: 50%; width: 60px; height: 60px; animation: spin 2s linear infinite; position: absolute; top: 50%; left: 50%; margin-left: -20px; margin-top: -20px; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } } .hidden { display: none; }</style>';
$aWidget['html'] .= '</head>';
$aWidget['html'] .= '<!--- /head -->';
$aWidget['html'] .= '<!--- body -->';
$aWidget['html'] .= '<body>';
$aWidget['html'] .= $aPage['content'];
$aWidget['html'] .= '<div id="loader-wrapper"><div id="loader" class="loader"></div></div>';
$aWidget['html'] .= '<footer>ziel--ide © [YEAR OF PUBLICATION] [WEBSITE NAME]. All rights reserved.<br/><div id="root"></div></footer>';
$aWidget['html'] .= '</body>';
$aWidget['html'] .= '<!--- /body -->';
$aWidget['html'] .= '<!--- /html -->';
$aWidget['html'] .= '</html>';


if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
    print PHP_EOL;
    print $aWidget['html'];
    #return true;
}
else {
    print $aWidget['html'];
    #return true;
    #exit();
}


?>





<script>

let menu = {
    onInit: function() {
        let links = document.querySelectorAll('.menu a[data-menu-files]');
        links.forEach((link) => {
            var action = link.getAttribute('data-menu-files');
            //var action = link.getAttribute('data-menu-files');
            //console.log(link.getAttribute('data-menu-files'));
            link.addEventListener('click', (event) => {
                var message = '{"menu":"'+ action +'", "path":"", "file":""}';
                socket.call(message);
            });
        });
    },
    filesNew: function() {
    },
    filesSave: function() {
    },
    filesSaveAll: function() {
    },
    filesOpen: function() {
    },
    filesOpenProject: function() {
    },
    filesReadOnly: function() {
    },
    filesReadOnlyAll: function() {
    }
}


let nav = {
    oninit: function() {
    }
}

let main = {
    oninit: function() {
    }
}

let aside = {
    oninit: function() {
        links.forEach((link) => {
            var action = link.getAttribute('data-menu-files');
            //var action = link.getAttribute('data-menu-files');
            //console.log(link.getAttribute('data-menu-files'));
            link.addEventListener('click', (event) => {
                var message = '{"menu":"'+ action +'", "file":""}';
                view.call(message);
            });
        });
    }
}

let footer = {
    oninit: function() {}
}

let socket = {
    peer: {},
	host: 'ws://127.0.0.1:44321/public/agent.php',
	onInit: function() {
        this.peer = new WebSocket(this.host);
        this.peer.addEventListener('readystatechange', this.onState);
        this.peer.addEventListener('open', this.onOpen);
        this.peer.addEventListener('message', this.onMessage);
        this.peer.addEventListener('close', this.onClose);
        this.peer.addEventListener('error', this.onError);
        console.log(this.peer);
    },
    call: function(event) {
        console.log("WebSocket send: ", event);
        this.peer.send(event);
    },
    onClose: function(event) {
        console.log("WebSocket close: ", event);
        document.getElementById('loader').classList.remove("hidden");
        //setInterval(1000);
        socket.onInit();
    },
    onError: function(error) {
        console.log("WebSocket error: ", event);
        document.getElementById('loader').classList.remove("hidden");
        //setInterval(1000);
        socket.onInit();
    },
    onMessage: function(event) {
        console.log('WebSocket onmessage: ', event);
        document.getElementById('root').innerHTML = "Message from server "+ event.data;
    },
    onOpen: function(event) {
        console.log("WebSocket open: ", event);
        document.getElementById('loader').classList.add("hidden");
/*
        var message = '{"a":1101,"b":22}';
        document.getElementById('open').innerHTML = JSON.stringify(event.data);
        
        setInterval(function() {
            if (event.currentTarget['bufferedAmount'] == 0)
            socket.peer.send(message);
            console.log(message);
        }, 10000);
        //var data = new ArrayBuffer(10000000);
*/
    },
    onState: function(state) {
        // this.socket.readyState
        console.log("WebSocket state: ", state);
    }
}

let layer = {
    onInit: function(state) {
        window.addEventListener("beforeunload", this.beforeunload);
        document.addEventListener("DOMContentLoaded", this.contentloaded);
    },
    beforeunload: function(event) {
        document.getElementById('loader').classList.remove("hidden");
    },
    contentloaded: function(event) {
        socket.onInit();
        document.getElementById('loader').classList.remove("hidden");
    }
}

menu.onInit()
layer.onInit();


</script>


