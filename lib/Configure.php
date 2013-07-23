<?php
class Configure
{
    static public function read($key, $default = null)
    {
        $config = include("config.php");
        if (isset($config[$key])) {
            return $config[$key];
        } else {
            return $default;
        }
    }
}
