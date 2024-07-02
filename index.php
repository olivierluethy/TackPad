<?php
require 'core/bootstrap.php';
require_once 'core/db_config.php';

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
	'deleteMultiple' => 'TackPadController@deleteMultiple',
	'deleteAllDone' => 'TackPadController@deleteAllDone',
	'deleteAllOpen' => 'TackPadController@deleteAllOpen',

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
    'name'     => DB_NAME,
    'username' => DB_USERNAME,
    'password' => DB_PASSWORD,
];

$router = new Router($routes);
$router->run($_GET['url'] ?? '');