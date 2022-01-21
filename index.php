<?php
require 'core/bootstrap.php';

$routes = [
	/* Hauptseiten */
	'/notes/home' => 'WelcomeController@index',
	'/notes/tackpad' => 'WelcomeController@tackpad',
	'/notes/about' => 'WelcomeController@about',

	/* Informationen hinzufügen */
	'/notes/create' => 'WelcomeController@create',

	/* Informationen löschen */
	'/notes/delete' => 'WelcomeController@delete',
	'/notes/deleteAll' => 'WelcomeController@deleteAll',

	/* Informationen bearbeiten */
	'/notes/edit' => 'WelcomeController@update',
	'/notes/erledigt' => 'WelcomeController@erledigt',
	'/notes/nichtmehrerledigt' => 'WelcomeController@nichtmehrerledigt',

	/* Login */
	'/notes/login' => 'WelcomeController@login',
	'/notes/config' => 'WelcomeController@config',
	'/notes/register' => 'WelcomeController@register',
	'/notes/logout' => 'WelcomeController@logout',
];

$db = [
	'name'     => 'tackpad',
	'username' => 'root',
	'password' => '',
];

$router = new Router($routes);
$router->run($_GET['url'] ?? '');