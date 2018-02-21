<?php

function adminer_object() {
	$adminerDir = './vendor/vrana/adminer/';
	$pluginsDir = "{$adminerDir}/plugins/";
	$designsDir = "{$adminerDir}/designs/";
	$designs    = array();

	include_once "{$pluginsDir}plugin.php";

	foreach(glob("{$pluginsDir}*.php") as $plugin) {
		include_once $plugin;
	}

	foreach(glob("{$designsDir}*") as $design) {
		if(($designName = str_replace($designsDir, '', $design)) != 'readme.txt') {
			$designs["{$design}/adminer.css"] = $designName;
		}
	}

	$plugins = array(
		new AdminerEnumOption,
		new AdminerDesigns($designs)
	);

	return new AdminerPlugin($plugins);
}

$versions = glob('./adminer-?.?.?.php');

require_once $versions[count($versions) - 1];
