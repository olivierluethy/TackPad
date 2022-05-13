<?php
require 'core/bootstrap.php';

$routes = [
	/* Hauptseiten */
	'' => 'TackPadController@index',
	'home' => 'TackPadController@index',

	/* Informationen hinzufügen */
	'create' => 'TackPadController@create',

	/* Informationen löschen */
	'delete' => 'TackPadController@delete',
	'deleteAll' => 'TackPadController@deleteAll',

	/* Informationen bearbeiten */
	'edit' => 'TackPadController@update',
	'erledigt' => 'TackPadController@erledigt',
	'nichtmehrerledigt' => 'TackPadController@nichtmehrerledigt',

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