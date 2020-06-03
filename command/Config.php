<?php

namespace Command;

use Symfony\Component\Yaml\Yaml;

final class Config {

	public static function generate() {
		$configFile = __DIR__ . '/../config.yaml';

		if(!file_exists($configFile)) {
			$parameters = array();

			$servers = array();
			$skip = false;
			do {
				echo 'Server name (for multiple servers, use empty name to skip): ';
				$serverName = trim(fgets(STDIN));
				$skip = !$serverName;
				if(!$skip) {
					echo 'Server address (you can add the port after a colon, default: localhost): ';
					$serverAddress = str_replace('localhost', '127.0.0.1', trim(fgets(STDIN)) ?: 'localhost');

					echo 'Server type (mysql|pgsql|sqlite|..., default: mysql)';
					$serverType = trim(fgets(STDIN)) ?: 'server';
					if($serverType == 'mysql') {
						$serverType = 'server';
					}

					$servers[$serverName] = array(
						'server'        => $serverAddress,
						'driver'        => $serverType
					);

					echo 'Use database password (yes/no, default: no): ';
					if(!preg_match('/^y(.*)/', fgets(STDIN))) {
						$servers[$serverName]['passwordless'] = true;
					}
				}
			} while(!$skip);

			if(count($servers) > 0) {
				$parameters['servers'] = $servers;
			} else {
				echo 'Use database password (yes/no, default: no): ';
				if(!preg_match('/^y(.*)/', fgets(STDIN))) {
					$parameters['passwordless'] = true;
				}
			}

			try {
				$config = array(
					'parameters' => $parameters
				);

				file_put_contents($configFile, Yaml::dump($config, 255));

				echo 'Config created successfully. You can change your settings manually in config.yaml.' . PHP_EOL;
			} catch(\Exception $ex) {
				echo $ex->getMessage() . PHP_EOL;
			}
		} else {
			echo 'Already configured. You can change your settings manually in config.yaml.' . PHP_EOL;
		}
	}
}
