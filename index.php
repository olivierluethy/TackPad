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

	/* Informationen löschen */
	'delete' => 'TackPadController@delete',
	'deleteAllDone' => 'TackPadController@deleteAllDone',
	'deleteAllOpen' => 'TackPadController@deleteAllOpen',

	/* Informationen bearbeiten */
	'showEditPage' => 'TackPadController@showEditPage',
	'erledigt' => 'TackPadController@erledigt',
	'unerledigt' => 'TackPadController@unerledigt',

	'getInfoFromId' => 'TackPadController@allInfoFromId',

	/* Login */
	'login' => 'TackPadController@login',
	'config' => 'TackPadController@config',
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