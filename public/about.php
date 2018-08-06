<?php

require_once('init.inc.php');

$view = new View_Container('about.tpl.html');
$view->assign('title', 'about - rk. tv');
$view->assign('page_id', 'page-about');
$view->assign('easter_egg', '/artwork/riina');
$view->render();