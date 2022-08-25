#!/bin/bash
ps aux | grep php
pkill -9 php
ps aux | grep php
php index.php & dig suiteziel.com @127.0.0.1