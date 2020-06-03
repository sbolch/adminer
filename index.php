<?php

require './vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

function adminer_object() {
	if(!file_exists('./config.yaml')) {
		if($_SERVER['SERVER_NAME'] == 'localhost') {
			die("Config file missing, please run 'composer install' or 'composer update' for creating one.");
		} else {
			die("Configuration error.");
		}
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
		$servers = new AdminerLoginServers($parameters['servers']);
		$plugins[] = $servers;

		foreach($servers->servers as $server) {
			if($servers->credentials()[0] == $server['server'] && isset($server['passwordless']) && $server['passwordless']) {
				$plugins[] = new AdminerLoginIp(array('127.0.0.1', '::1'));
				break;
			}
		}

	} elseif(isset($parameters['passwordless']) && $parameters['passwordless']) {
		$plugins[] = new AdminerLoginIp(array('127.0.0.1', '::1'));
	}

	return new AdminerPlugin($plugins);
}

$versions = glob('./adminer-?.?.?.php');
natsort($versions);
require_once end($versions);
