<?php

require './vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

function adminer_object() {
	if($_SERVER['SERVER_NAME'] == 'localhost' && !file_exists('./config.yaml')) {
		die("Config file missing, please run 'composer install' or 'composer update' for creating one.");
	}

	$adminerDir = './vendor/vrana/adminer';
	$pluginsDir = "$adminerDir/plugins";
	$designsDir = "$adminerDir/designs";
	$designs    = array();
	$config		= Yaml::parseFile('./config.yaml');
	$parameters	= $config['parameters'];

	include_once "$pluginsDir/plugin.php";

	foreach(glob("$pluginsDir/*.php") as $plugin) {
		include_once $plugin;
	}

	foreach(glob("$designsDir/*") as $design) {
		if(($designName = str_replace("$designsDir/", '', $design)) != 'readme.txt') {
			$designs["$design/adminer.css"] = $designName;
		}
	}
	$designs['./vendor/archeloth/adminer-theme/barnabas/adminer.css'] = 'barnabas';

	array_multisort($designs);

	$plugins = array(
		new AdminerEnumOption,
		new AdminerDesigns($designs)
	);

	if($parameters['servers'] && count($parameters['servers']) > 0) {
		$plugins[] = new AdminerLoginServers($parameters['servers']);
	}

	if($_SERVER['SERVER_NAME'] == 'localhost' && !$parameters['database_password']) {
		$plugins[] = new AdminerLoginPasswordLess($parameters['password_hash']);
	}

	return new AdminerPlugin($plugins);
}

$versions = glob('./adminer-?.?.?.php');

require_once $versions[count($versions) - 1];
