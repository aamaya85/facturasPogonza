<?php

  require __DIR__.'/vendor/autoload.php';
	require_once __DIR__.'/src/routes.php'; /* Load external routes file */
	require_once __DIR__.'/src/lib/Helpers/Helper.php';
	
	define('CONFIG', './config/config.ini');
	define('TEMPLATES_PATH', './templates');

	use \Pecee\SimpleRouter\SimpleRouter;

	// Start the routing
	SimpleRouter::start();


?>
