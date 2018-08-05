<?php

$config = require_once('init.inc.php');
require_once($config->app_class_dir . '/view/view_riina.class.php');
require_once($config->app_class_dir . '/model/model_whatsnew.class.php');

$model = new Model_Whatsnew($config->db);
$whatsnew = $model->get_all();

$view = new View_Riina('index.tpl.html');
$view->assign('title', 'rk. tv : あーけーどてぃーゔぃー');
$view->assign('page_id', 'page-top');
$view->assignByRef('whatsnew', $whatsnew);
$view->render();
