<?php

return array(
	'about'        => 'top/about',
	'music/(.*)'   => 'music/detail/$1',
	'artwork/(.*)' => 'artwork/detail/$1',
	'preview/(.*)' => 'preview/detail/$1',
	'iframe/(.*)'  => 'iframe/detail/$1',
);
