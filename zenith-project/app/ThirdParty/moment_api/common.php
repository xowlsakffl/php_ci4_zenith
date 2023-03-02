<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set('memory_limit','-1');
ini_set('max_execution_time', 0);

exec("ps ax | grep -i moment_api | grep -v grep", $exec);
foreach($exec as $v) $proc[] = preg_replace('/^.+php\s(.+)$/', '$1', $v);
