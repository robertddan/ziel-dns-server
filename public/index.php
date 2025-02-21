<?php

function exception_handler (Throwable $exception) {
    print PHP_EOL. $exception->getMessage() .PHP_EOL.
    'On file: '.$exception->getFile() .PHP_EOL.
    'On line: '. $exception->getLine() .PHP_EOL.
    $exception->getTraceAsString() .PHP_EOL;
}

function throw_exception ($sException) {
    throw new Exception($sException);
}

set_exception_handler('exception_handler');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS . '..' . DS);
define('CONFIG', ROOT . DS . 'config' . DS);
define('VENDOR', ROOT . DS . 'vendor' . DS);

require(CONFIG . DS . 'bootstrap.php');


$aRouter = array();
$aRouter = parse_url('/'. $_SERVER["REQUEST_URI"]);


if (isset($aRouter['host'])) #["host"]=>"favicon.ico"
switch ($aRouter['host']) {
    case 'favicon.ico':
        header('Content-Type: text/x-icon');
        print file_get_contents(ROOT .'public'. DS .'favicon.ico');
        exit();
        break;
}

print '<pre>';
use Ziel\Vm;
$oVm = new Vm();
$oVm->run();
print '</pre>';

?>