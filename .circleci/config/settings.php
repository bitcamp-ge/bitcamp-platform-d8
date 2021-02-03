<?php

/**
 * Database settings
 */
$databases['default']['default'] = array (
  'database' => 'bitcamp',
  'username' => 'root',
  'password' => 'toor',
  'prefix' => 'latte_',
  'host' => '127.0.0.1',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

/**
 * Config directory.
 */
$settings['config_sync_directory'] = '../config/sync';
