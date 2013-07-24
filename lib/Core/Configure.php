<?php
class Configure
{
/**
 * Array of values currently stored in Configure.
 *
 * @var array
 */
    protected static $_values = array(
        'debug' => 0
    );

/**
 * Loads stored configuration information from a resource. You can add
 * config file resource readers with `Configure::config()`.
 *
 * Loaded configuration information will be merged with the current
 * runtime configuration. You can load configuration files from plugins
 * by preceding the filename with the plugin name.
 *
 * `Configure::load('Users.user', 'default')`
 *
 * Would load the 'user' config file using the default config reader. You can load
 * app config files by giving the name of the resource you want loaded.
 *
 * `Configure::load('setup', 'default');`
 *
 * If using `default` config and no reader has been configured for it yet,
 * one will be automatically created using PhpReader
 *
 * @link http://book.cakephp.org/2.0/en/development/configuration.html#Configure::load
 * @param string $key name of configuration resource to load.
 * @param string $config Name of the configured reader to use to read the resource identified by $key.
 * @param boolean $merge if config files should be merged instead of simply overridden
 * @return mixed false if file not found, void if load successful.
 * @throws ConfigureException Will throw any exceptions the reader raises.
 */
    public static function load($key, $config = 'default', $merge = true) {
        if (defined("CONFIG_DIR")) {
            $filename = sprintf("%s/%s.php", CONFIG_DIR, $key);
        } else {
            $filename = sprintf("%s.php", $key);
        }

        $values = array();
        if (file_exists($filename)) {
            include($filename);
            $values = $config;
        }

        if ($merge) {
            $keys = array_keys($values);
            foreach ($keys as $key) {
                if (($c = self::read($key)) && is_array($values[$key]) && is_array($c)) {
                    $values[$key] = Hash::merge($c, $values[$key]);
                }
            }
        }

        return self::write($values);
    }

/**
 * Used to store a dynamic variable in Configure.
 *
 * Usage:
 * {{{
 * Configure::write('One.key1', 'value of the Configure::One[key1]');
 * Configure::write(array('One.key1' => 'value of the Configure::One[key1]'));
 * Configure::write('One', array(
 *     'key1' => 'value of the Configure::One[key1]',
 *     'key2' => 'value of the Configure::One[key2]'
 * );
 *
 * Configure::write(array(
 *     'One.key1' => 'value of the Configure::One[key1]',
 *     'One.key2' => 'value of the Configure::One[key2]'
 * ));
 * }}}
 *
 * @link http://book.cakephp.org/2.0/en/development/configuration.html#Configure::write
 * @param array $config Name of var to write
 * @param mixed $value Value to set for var
 * @return boolean True if write was successful
 */
    public static function write($config, $value = null) {
        if (!is_array($config)) {
            $config = array($config => $value);
        }

        foreach ($config as $name => $value) {
            self::$_values = Hash::insert(self::$_values, $name, $value);
        }

        if (isset($config['debug']) && function_exists('ini_set')) {
            if (self::$_values['debug']) {
                ini_set('display_errors', 1);
            } else {
                ini_set('display_errors', 0);
            }
        }
        return true;
    }

/**
 * Used to read information stored in Configure. Its not
 * possible to store `null` values in Configure.
 *
 * Usage:
 * {{{
 * Configure::read('Name'); will return all values for Name
 * Configure::read('Name.key'); will return only the value of Configure::Name[key]
 * }}}
 *
 * @link http://book.cakephp.org/2.0/en/development/configuration.html#Configure::read
 * @param string $var Variable to obtain. Use '.' to access array elements.
 * @return mixed value stored in configure, or null.
 */
    public static function read($var = null) {
        if ($var === null) {
            return self::$_values;
        }
        return Hash::get(self::$_values, $var);
    }
}
