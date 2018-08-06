<?php

require_once('init.inc.php');

$model = new Model_Whatsnew($config->db);
$whatsnew = $model->get_all();

$view = new View_Container('index.tpl.html');
$view->assign('title', 'rk. tv : あーけーどてぃーゔぃー');
$view->assign('page_id', 'page-top');
$view->assignByRef('whatsnew', $whatsnew);
$view->render();
