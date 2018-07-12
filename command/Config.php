<?php

namespace Command;

use Symfony\Component\Yaml\Yaml;

class Config {
	
	public function generate() {
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

				echo 'Config created successfully. You can change it manually in the created config.yaml file.';
			} catch(\Exception $ex) {
				echo $ex->getMessage();
			}
		} else {
			echo 'Config already exists. You can change it manually in the created config.yaml file.';
		}
	}
}