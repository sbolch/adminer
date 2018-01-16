<?php

function adminer_object() {
	include_once './vendor/vrana/adminer/plugins/plugin.php';

	foreach(glob('./vendor/vrana/adminer/plugins/*.php') as $plugin) {
		include_once "./$plugin";
	}

	$plugins = array(
		new AdminerEnumOption
	);

	return new AdminerPlugin($plugins);
}

include './vendor/vrana/adminer/adminer-4.3.1.php';
