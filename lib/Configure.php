<?php
require_once('Hash.php');

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
