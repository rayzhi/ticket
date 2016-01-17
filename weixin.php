<?php

$_GET['m'] = 'Wechat';
if ( !isset($_GET['c']))
	$_GET['c'] = 'Index';
if ( !isset($_GET['a']))
	$_GET['a'] = 'index';

include('index.php');

