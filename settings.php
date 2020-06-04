<?php

require './vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

const SETTINGS_FILE = './settings.yaml';

if($_POST && !empty($_POST)) {
    $settings = array();

    unset($_POST['submit']);

    if(isset($_POST['passwordless'])) {
        $settings['passwordless'] = (bool)$_POST['passwordless'];
        unset($_POST['passwordless']);
    }

    $servers = array();
    foreach($_POST as $server) {
        $servers[$server['name']]['server'] = $server['server'] ?: '127.0.0.1';
        $servers[$server['name']]['server'] .= $server['port'] ? ":{$server['port']}" : '';
        $servers[$server['name']]['driver'] = $server['driver'];

        if(isset($server['passwordless'])) {
            $servers[$server['name']]['passwordless'] = (bool)$server['passwordless'];
        }
    }

    if(count($servers) > 0) {
        $settings['servers'] = $servers;
        unset($settings['passwordless']);
    }

    file_put_contents(SETTINGS_FILE, Yaml::dump($settings, 255));

    header('location:/');
} else {
    $servers = array();

    if(file_exists(SETTINGS_FILE)) {
        $config  = Yaml::parseFile(SETTINGS_FILE);
        $servers = $config['servers'];

        foreach($servers as $name => $server) {
            list($url, $port) = explode(':', $server['server']);
            if($port) {
                $servers[$name]['server'] = $url;
                $servers[$name]['port']   = $port;
            }
        }
    }

    $drivers = array(
        'server'     => 'MySQL',
        'sqlite'     => 'SQLite 3',
        'sqlite2'    => 'SQLite 2',
        'pgsql'      => 'PostrgreSQL',
        'oracle'     => 'Oracle',
        'mssql'      => 'MS SQL',
        'firebird'   => 'Firebird',
        'simpledb'   => 'SimpleDB',
        'mongo'      => 'MongoDB',
        'elastic'    => 'Elasticsearch',
        'clickhouse' => 'ClickHouse'
    );

    require_once 'settings.phtml';
}
