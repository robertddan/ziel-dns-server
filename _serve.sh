#!/bin/bash
ps aux | grep php
pkill -9 php
ps aux | grep php

#php index.php & dig +trace +answer +multiline suiteziel.com @127.0.0.1 #ANY 
php index.php & dig www.suiteziel.com @127.0.0.1 