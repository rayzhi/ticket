<?php

$_GET['m'] = 'Wechat';
if ( !isset($_GET['c']))
	$_GET['c'] = 'Wechat';
if ( !isset($_GET['a']))
	$_GET['a'] = 'index';

include('index.php');

