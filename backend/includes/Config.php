<?php

class Config {
    private static $config = [];

    public static function load() {
        if (empty(self::$config)) {
            $envFile = dirname(dirname(__DIR__)) . '/.env';
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, '=') !== false && $line[0] !== '#') {
                        list($key, $value) = explode('=', $line, 2);
                        self::$config[trim($key)] = trim($value);
                    }
                }
            }
        }
    }

    public static function get($key) {
        if (empty(self::$config)) {
            self::load();
        }
        return isset(self::$config[$key]) ? self::$config[$key] : null;
    }
}

Config::load();