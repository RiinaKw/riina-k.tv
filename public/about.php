<?php

$config = require_once('init.inc.php');
require_once($config->app_class_dir . '/view/container.class.php');

$view = new View_Riina('about.tpl.html');
$view->assign('title', 'about - rk. tv');
$view->assign('page_id', 'page-about');
$view->assign('easter_egg', '/artwork/riina');
$view->render();