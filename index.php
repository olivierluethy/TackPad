<?php
require 'core/bootstrap.php';

$routes = [
	/* Hauptseiten */
	'' => 'TackPadController@index',
	'home' => 'TackPadController@index',

	'getTaskData' => 'TackPadController@getTaskData',

	'edit' => 'TackPadController@edit',

	/* Informationen hinzufügen */
	'create' => 'TackPadController@create',

	/* Informationen löschen */
	'delete' => 'TackPadController@delete',
	'deleteAllNichtZuSpaetOffeneTasks' => 'TackPadController@deleteAllNichtZuSpaetOffeneTasks',

	/* Informationen bearbeiten */
	'showEditPage' => 'TackPadController@showEditPage',
	'erledigt' => 'TackPadController@erledigt',
	'nichtmehrerledigt' => 'TackPadController@nichtmehrerledigt',

	'getInfoFromId' => 'TackPadController@allInfoFromId',

	/* Login */
	'login' => 'TackPadController@login',
	'config' => 'TackPadController@config',
	'register' => 'TackPadController@register',
	'logout' => 'TackPadController@logout',
];

$db = [
	'name'     => 'tackpad',
	'username' => 'root',
	'password' => '',
];

$router = new Router($routes);
$router->run($_GET['url'] ?? '');