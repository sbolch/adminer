<?php

require './vendor/autoload.php';

use Plugins\AdminerSettings;
use Symfony\Component\Yaml\Yaml;

const SETTINGS_FILE = './settings.yaml';

function adminer_object() {
	if(!file_exists(SETTINGS_FILE)) {
		header('location:/settings.php');
	}

	$adminerDir = './vendor/vrana/adminer';
	$pluginsDir = "$adminerDir/plugins";
	$designsDir = "$adminerDir/designs";
	$designs    = array();
	$config		= Yaml::parseFile(SETTINGS_FILE);
	$servers	= $config['servers'];

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
	$designs['./vendor/niyko/hydra-dark-theme-for-adminer/adminer.css'] = 'hydra';

	array_multisort($designs);

	$plugins = array(
		new AdminerEnumOption,
		new AdminerDesigns($designs),
		new AdminerSettings
	);

	if($servers && count($servers) > 0) {
		$servers = new AdminerLoginServers($servers);
		$plugins[] = $servers;

		foreach($servers->servers as $server) {
			if($servers->credentials()[0] == $server['server'] && isset($server['passwordless']) && $server['passwordless']) {
				$plugins[] = new AdminerLoginIp(array('127.0.0.1', '::1'));
				break;
			}
		}

	} elseif(isset($config['passwordless']) && $config['passwordless']) {
		$plugins[] = new AdminerLoginIp(array('127.0.0.1', '::1'));
	}

	return new AdminerPlugin($plugins);
}

$versions = glob('./adminer-?.?.?.php');
natsort($versions);
require_once end($versions);
