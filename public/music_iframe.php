<?php

$config = require_once('init.inc.php');
require_once(INCLUDE_DIR . '/view/view.class.php');

$preview_url = ROOT_URL . '/preview' . $_SERVER['PATH_INFO'];
$download_url = $preview_url . '/download';

$view = new View('music_iframe.tpl.html');
$view->assignByRef('preview_url', $preview_url);
$view->assignByRef('download_url', $download_url);
$view->render();