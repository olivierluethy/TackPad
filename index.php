<?php
require 'core/bootstrap.php';
require_once 'core/db_config.php';

$routes = [
	/* Hauptseiten */
	'' => 'TackPadController@index',
	'home' => 'TackPadController@index',

	/* Kalender */
	'calendar' => 'TackPadController@calendar',
	'calendarevents' => 'TackPadController@calendarEvents',
	'updatetaskdate' => 'TackPadController@updateTaskDate',
	'calendar.ics' => 'TackPadController@exportIcs',

	'edit' => 'TackPadController@edit',

	/* Informationen hinzufügen */
	'create' => 'TackPadController@create',

	/* Aufgabe teilen */
	'share' => 'TackPadController@share',

	/* Profilbild */
	'avatar' => 'TackPadController@updateAvatar',

	/* Informationen löschen */
	'delete' => 'TackPadController@delete',

	/* Status ändern */
	'erledigt' => 'TackPadController@erledigt',
	'unerledigt' => 'TackPadController@unerledigt',

	/* Login */
	'login' => 'TackPadController@login',
	'register' => 'TackPadController@register',
	'logout' => 'TackPadController@logout',
];

$db = [
	'name' => DB_NAME,
	'username' => DB_USERNAME,
	'password' => DB_PASSWORD,
];

$router = new Router($routes);
$router->run($_GET['url'] ?? '');