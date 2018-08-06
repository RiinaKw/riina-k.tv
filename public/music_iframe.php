<?php

$config = require_once('init.inc.php');
require_once($config->app_class_dir . '/view.class.php');

$preview_url = $config->root_url . '/preview' . $_SERVER['PATH_INFO'];
$download_url = $preview_url . '/download';

$view = new View('music_iframe.tpl.html');
$view->assignByRef('preview_url', $preview_url);
$view->assignByRef('download_url', $download_url);
$view->render();