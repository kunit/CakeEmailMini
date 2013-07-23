<?php
require_once('Hash.php');

class Configure
{
    static public function read($key)
    {
        if (defined("CONFIG_DIR")) {
            $config = include(CONFIG_DIR . DIRECTORY_SEPARATOR . "config.php");
        } else {
            $config = include("config.php");
        }
        return Hash::get($config, $key);
    }
}
