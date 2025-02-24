<?php

namespace App\Suiteziel\Http;


class SuitezielHttp
{
    function SuitezielHttp() {
        $someInstance
    ->create()
    ->prepare()
    ->run();
    
        $app->get('/hello/{name}', function ($name) use ($app) {
            return 'Hello ' . $app->escape($name);
        });    
    }
}

//EOF