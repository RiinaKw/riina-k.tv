<?php

require_once('init.inc.php');

$slug = '';
if ( isset($_SERVER['PATH_INFO']) ) {
	$arr = explode( '/', $_SERVER['PATH_INFO'] );
	$slug = ( isset($arr[1]) ? $arr[1] : '' );
}

$model = new Model_Track($config->db);
$categories = $model->get_as_category();

$view = new View_Container('music.tpl.html');
$view->assign('page_id', 'page-music');
$view->assignByRef('categories', $categories);
if ($slug) {
	$track = $model->get_by_slug($slug);
	$view->assignByRef('track', $track);
	$view->assignByRef('title', $track['title'] . ' - rk. tv');
} else {
	$view->assign('title', 'music - rk. tv');
}
$view->render();
