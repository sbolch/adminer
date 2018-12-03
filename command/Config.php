<?php

namespace Command;

use Symfony\Component\Yaml\Yaml;

final class Config {
	
	public static function generate() {
		$configFile = __DIR__ . '/../config.yaml';

		if(!file_exists($configFile)) {
			$parameters = array();

			echo 'Use database password (yes/no, default: no): ';

			$dbPassword = trim(fgets(STDIN)) == 'yes';

			$parameters['database_password'] = $dbPassword;

			echo 'Password' . ($dbPassword ? ' (for Adminer login only)' : '') . ': ';

			$password = trim(fgets(STDIN));

			$parameters['password_hash'] = password_hash($password, PASSWORD_DEFAULT);

			try {
				$config = array(
					'parameters' => $parameters
				);

				file_put_contents($configFile, Yaml::dump($config));

				echo 'Config created successfully. You can change it manually in the created config.yaml file.' . PHP_EOL;
			} catch(\Exception $ex) {
				echo $ex->getMessage() . PHP_EOL;
			}
		} else {
			echo 'Config already exists. You can change it manually in the created config.yaml file.' . PHP_EOL;
		}
	}
}