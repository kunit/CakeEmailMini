<?php
/**
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 1.2.0.3830
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Validation Class. Used for validation of model data
 *
 * Offers different validation methods.
 *
 * @package       Cake.Utility
 */
class Validation {

/**
 * Some complex patterns needed in multiple places
 *
 * @var array
 */
    protected static $_pattern = array(
        'hostname' => '(?:[_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})'
    );

/**
 * Holds an array of errors messages set in this class.
 * These are used for debugging purposes
 *
 * @var array
 */
    public static $errors = array();

/**
 * Checks that a string contains something other than whitespace
 *
 * Returns true if string contains something other than whitespace
 *
 * $check can be passed as an array:
 * array('check' => 'valueToCheck');
 *
 * @param string|array $check Value to check
 * @return boolean Success
 */
    public static function notEmpty($check) {
        if (is_array($check)) {
            extract(self::_defaults($check));
        }

        if (empty($check) && $check != '0') {
            return false;
        }
        return self::_check($check, '/[^\s]+/m');
    }

/**
 * Checks that a string contains only integer or letters
 *
 * Returns true if string contains only integer or letters
 *
 * $check can be passed as an array:
 * array('check' => 'valueToCheck');
 *
 * @param string|array $check Value to check
 * @return boolean Success
 */
    public static function alphaNumeric($check) {
        if (is_array($check)) {
            extract(self::_defaults($check));
        }

        if (empty($check) && $check != '0') {
            return false;
        }
        return self::_check($check, '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]+$/Du');
    }

/**
 * Checks that a string length is within s specified range.
 * Spaces are included in the character count.
 * Returns true is string matches value min, max, or between min and max,
 *
 * @param string $check Value to check for length
 * @param integer $min Minimum value in range (inclusive)
 * @param integer $max Maximum value in range (inclusive)
 * @return boolean Success
 */
    public static function between($check, $min, $max) {
        $length = mb_strlen($check);
        return ($length >= $min && $length <= $max);
    }

/**
 * Returns true if field is left blank -OR- only whitespace characters are present in its value
 * Whitespace characters include Space, Tab, Carriage Return, Newline
 *
 * $check can be passed as an array:
 * array('check' => 'valueToCheck');
 *
 * @param string|array $check Value to check
 * @return boolean Success
 */
    public static function blank($check) {
        if (is_array($check)) {
            extract(self::_defaults($check));
        }
        return !self::_check($check, '/[^\\s]/');
    }

/**
 * Used when a custom regular expression is needed.
 *
 * @param string|array $check When used as a string, $regex must also be a valid regular expression.
 *                                As and array: array('check' => value, 'regex' => 'valid regular expression')
 * @param string $regex If $check is passed as a string, $regex must also be set to valid regular expression
 * @return boolean Success
 */
    public static function custom($check, $regex = null) {
        if (is_array($check)) {
            extract(self::_defaults($check));
        }
        if ($regex === null) {
            self::$errors[] = __d('cake_dev', 'You must define a regular expression for Validation::custom()');
            return false;
        }
        return self::_check($check, $regex);
    }

/**
 * Date validation, determines if the string passed is a valid date.
 * keys that expect full month, day and year will validate leap years
 *
 * @param string $check a valid date string
 * @param string|array $format Use a string or an array of the keys below. Arrays should be passed as array('dmy', 'mdy', etc)
 *           Keys: dmy 27-12-2006 or 27-12-06 separators can be a space, period, dash, forward slash
 *                 mdy 12-27-2006 or 12-27-06 separators can be a space, period, dash, forward slash
 *                 ymd 2006-12-27 or 06-12-27 separators can be a space, period, dash, forward slash
 *                 dMy 27 December 2006 or 27 Dec 2006
 *                 Mdy December 27, 2006 or Dec 27, 2006 comma is optional
 *                 My December 2006 or Dec 2006
 *                 my 12/2006 separators can be a space, period, dash, forward slash
 * @param string $regex If a custom regular expression is used this is the only validation that will occur.
 * @return boolean Success
 */
    public static function date($check, $format = 'ymd', $regex = null) {
        if (!is_null($regex)) {
            return self::_check($check, $regex);
        }

        $regex['dmy'] = '%^(?:(?:31(\\/|-|\\.|\\x20)(?:0?[13578]|1[02]))\\1|(?:(?:29|30)(\\/|-|\\.|\\x20)(?:0?[1,3-9]|1[0-2])\\2))(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$|^(?:29(\\/|-|\\.|\\x20)0?2\\3(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\\d|2[0-8])(\\/|-|\\.|\\x20)(?:(?:0?[1-9])|(?:1[0-2]))\\4(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$%';
        $regex['mdy'] = '%^(?:(?:(?:0?[13578]|1[02])(\\/|-|\\.|\\x20)31)\\1|(?:(?:0?[13-9]|1[0-2])(\\/|-|\\.|\\x20)(?:29|30)\\2))(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$|^(?:0?2(\\/|-|\\.|\\x20)29\\3(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:(?:0?[1-9])|(?:1[0-2]))(\\/|-|\\.|\\x20)(?:0?[1-9]|1\\d|2[0-8])\\4(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$%';
        $regex['ymd'] = '%^(?:(?:(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(\\/|-|\\.|\\x20)(?:0?2\\1(?:29)))|(?:(?:(?:1[6-9]|[2-9]\\d)?\\d{2})(\\/|-|\\.|\\x20)(?:(?:(?:0?[13578]|1[02])\\2(?:31))|(?:(?:0?[1,3-9]|1[0-2])\\2(29|30))|(?:(?:0?[1-9])|(?:1[0-2]))\\2(?:0?[1-9]|1\\d|2[0-8]))))$%';
        $regex['dMy'] = '/^((31(?!\\ (Feb(ruary)?|Apr(il)?|June?|(Sep(?=\\b|t)t?|Nov)(ember)?)))|((30|29)(?!\\ Feb(ruary)?))|(29(?=\\ Feb(ruary)?\\ (((1[6-9]|[2-9]\\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))|(0?[1-9])|1\\d|2[0-8])\\ (Jan(uary)?|Feb(ruary)?|Ma(r(ch)?|y)|Apr(il)?|Ju((ly?)|(ne?))|Aug(ust)?|Oct(ober)?|(Sep(?=\\b|t)t?|Nov|Dec)(ember)?)\\ ((1[6-9]|[2-9]\\d)\\d{2})$/';
        $regex['Mdy'] = '/^(?:(((Jan(uary)?|Ma(r(ch)?|y)|Jul(y)?|Aug(ust)?|Oct(ober)?|Dec(ember)?)\\ 31)|((Jan(uary)?|Ma(r(ch)?|y)|Apr(il)?|Ju((ly?)|(ne?))|Aug(ust)?|Oct(ober)?|(Sep)(tember)?|(Nov|Dec)(ember)?)\\ (0?[1-9]|([12]\\d)|30))|(Feb(ruary)?\\ (0?[1-9]|1\\d|2[0-8]|(29(?=,?\\ ((1[6-9]|[2-9]\\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))))\\,?\\ ((1[6-9]|[2-9]\\d)\\d{2}))$/';
        $regex['My'] = '%^(Jan(uary)?|Feb(ruary)?|Ma(r(ch)?|y)|Apr(il)?|Ju((ly?)|(ne?))|Aug(ust)?|Oct(ober)?|(Sep(?=\\b|t)t?|Nov|Dec)(ember)?)[ /]((1[6-9]|[2-9]\\d)\\d{2})$%';
        $regex['my'] = '%^((0[123456789]|10|11|12)([- /.])(([1][9][0-9][0-9])|([2][0-9][0-9][0-9])))$%';

        $format = (is_array($format)) ? array_values($format) : array($format);
        foreach ($format as $key) {
            if (self::_check($check, $regex[$key]) === true) {
                return true;
            }
        }
        return false;
    }

/**
 * Validates a datetime value
 * All values matching the "date" core validation rule, and the "time" one will be valid
 *
 * @param string $check Value to check
 * @param string|array $dateFormat Format of the date part
 * Use a string or an array of the keys below. Arrays should be passed as array('dmy', 'mdy', etc)
 * ## Keys:
 *
 * - dmy 27-12-2006 or 27-12-06 separators can be a space, period, dash, forward slash
 * - mdy 12-27-2006 or 12-27-06 separators can be a space, period, dash, forward slash
 * - ymd 2006-12-27 or 06-12-27 separators can be a space, period, dash, forward slash
 * - dMy 27 December 2006 or 27 Dec 2006
 * - Mdy December 27, 2006 or Dec 27, 2006 comma is optional
 * - My December 2006 or Dec 2006
 * - my 12/2006 separators can be a space, period, dash, forward slash
 * @param string $regex Regex for the date part. If a custom regular expression is used this is the only validation that will occur.
 * @return boolean True if the value is valid, false otherwise
 * @see Validation::date
 * @see Validation::time
 */
    public static function datetime($check, $dateFormat = 'ymd', $regex = null) {
        $valid = false;
        $parts = explode(' ', $check);
        if (!empty($parts) && count($parts) > 1) {
            $time = array_pop($parts);
            $date = implode(' ', $parts);
            $valid = self::date($date, $dateFormat, $regex) && self::time($time);
        }
        return $valid;
    }

/**
 * Time validation, determines if the string passed is a valid time.
 * Validates time as 24hr (HH:MM) or am/pm ([H]H:MM[a|p]m)
 * Does not allow/validate seconds.
 *
 * @param string $check a valid time string
 * @return boolean Success
 */
    public static function time($check) {
        return self::_check($check, '%^((0?[1-9]|1[012])(:[0-5]\d){0,2} ?([AP]M|[ap]m))$|^([01]\d|2[0-3])(:[0-5]\d){0,2}$%');
    }

/**
 * Boolean validation, determines if value passed is a boolean integer or true/false.
 *
 * @param string $check a valid boolean
 * @return boolean Success
 */
    public static function boolean($check) {
        $booleanList = array(0, 1, '0', '1', true, false);
        return in_array($check, $booleanList, true);
    }

/**
 * Checks that a value is a valid decimal. Both the sign and exponent are optional.
 *
 * Valid Places:
 *
 * - null => Any number of decimal places, including none. The '.' is not required.
 * - true => Any number of decimal places greater than 0, or a float|double. The '.' is required.
 * - 1..N => Exactly that many number of decimal places. The '.' is required.
 *
 * @param float $check The value the test for decimal
 * @param integer $places
 * @param string $regex If a custom regular expression is used, this is the only validation that will occur.
 * @return boolean Success
 */
    public static function decimal($check, $places = null, $regex = null) {
        if (is_null($regex)) {
            $lnum = '[0-9]+';
            $dnum = "[0-9]*[\.]{$lnum}";
            $sign = '[+-]?';
            $exp = "(?:[eE]{$sign}{$lnum})?";

            if ($places === null) {
                $regex = "/^{$sign}(?:{$lnum}|{$dnum}){$exp}$/";

            } elseif ($places === true) {
                if (is_float($check) && floor($check) === $check) {
                    $check = sprintf("%.1f", $check);
                }
                $regex = "/^{$sign}{$dnum}{$exp}$/";

            } elseif (is_numeric($places)) {
                $places = '[0-9]{' . $places . '}';
                $dnum = "(?:[0-9]*[\.]{$places}|{$lnum}[\.]{$places})";
                $regex = "/^{$sign}{$dnum}{$exp}$/";
            }
        }
        return self::_check($check, $regex);
    }

/**
 * Validates for an email address.
 *
 * Only uses getmxrr() checking for deep validation if PHP 5.3.0+ is used, or
 * any PHP version on a non-windows distribution
 *
 * @param string $check Value to check
 * @param boolean $deep Perform a deeper validation (if true), by also checking availability of host
 * @param string $regex Regex to use (if none it will use built in regex)
 * @return boolean Success
 */
    public static function email($check, $deep = false, $regex = null) {
        if (is_array($check)) {
            extract(self::_defaults($check));
        }

        if (is_null($regex)) {
            $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@' . self::$_pattern['hostname'] . '$/i';
        }
        $return = self::_check($check, $regex);
        if ($deep === false || $deep === null) {
            return $return;
        }

        if ($return === true && preg_match('/@(' . self::$_pattern['hostname'] . ')$/i', $check, $regs)) {
            if (function_exists('getmxrr') && getmxrr($regs[1], $mxhosts)) {
                return true;
            }
            if (function_exists('checkdnsrr') && checkdnsrr($regs[1], 'MX')) {
                return true;
            }
            return is_array(gethostbynamel($regs[1]));
        }
        return false;
    }

/**
 * Check that value is exactly $comparedTo.
 *
 * @param mixed $check Value to check
 * @param mixed $comparedTo Value to compare
 * @return boolean Success
 */
    public static function equalTo($check, $comparedTo) {
        return ($check === $comparedTo);
    }

/**
 * Check that value has a valid file extension.
 *
 * @param string|array $check Value to check
 * @param array $extensions file extensions to allow. By default extensions are 'gif', 'jpeg', 'png', 'jpg'
 * @return boolean Success
 */
    public static function extension($check, $extensions = array('gif', 'jpeg', 'png', 'jpg')) {
        if (is_array($check)) {
            return self::extension(array_shift($check), $extensions);
        }
        $extension = strtolower(pathinfo($check, PATHINFO_EXTENSION));
        foreach ($extensions as $value) {
            if ($extension === strtolower($value)) {
                return true;
            }
        }
        return false;
    }

/**
 * Validation of an IP address.
 *
 * @param string $check The string to test.
 * @param string $type The IP Protocol version to validate against
 * @return boolean Success
 */
    public static function ip($check, $type = 'both') {
        $type = strtolower($type);
        $flags = 0;
        if ($type === 'ipv4') {
            $flags = FILTER_FLAG_IPV4;
        }
        if ($type === 'ipv6') {
            $flags = FILTER_FLAG_IPV6;
        }
        return (boolean)filter_var($check, FILTER_VALIDATE_IP, array('flags' => $flags));
    }

/**
 * Checks whether the length of a string is greater or equal to a minimal length.
 *
 * @param string $check The string to test
 * @param integer $min The minimal string length
 * @return boolean Success
 */
    public static function minLength($check, $min) {
        return mb_strlen($check) >= $min;
    }

/**
 * Checks whether the length of a string is smaller or equal to a maximal length..
 *
 * @param string $check The string to test
 * @param integer $max The maximal string length
 * @return boolean Success
 */
    public static function maxLength($check, $max) {
        return mb_strlen($check) <= $max;
    }

/**
 * Validate that a number is in specified range.
 * if $lower and $upper are not set, will return true if
 * $check is a legal finite on this platform
 *
 * @param string $check Value to check
 * @param integer $lower Lower limit
 * @param integer $upper Upper limit
 * @return boolean Success
 */
    public static function range($check, $lower = null, $upper = null) {
        if (!is_numeric($check)) {
            return false;
        }
        if (isset($lower) && isset($upper)) {
            return ($check > $lower && $check < $upper);
        }
        return is_finite($check);
    }

/**
 * Checks that a value is a valid URL according to http://www.w3.org/Addressing/URL/url-spec.txt
 *
 * The regex checks for the following component parts:
 *
 * - a valid, optional, scheme
 * - a valid ip address OR
 *   a valid domain name as defined by section 2.3.1 of http://www.ietf.org/rfc/rfc1035.txt
 *   with an optional port number
 * - an optional valid path
 * - an optional query string (get parameters)
 * - an optional fragment (anchor tag)
 *
 * @param string $check Value to check
 * @param boolean $strict Require URL to be prefixed by a valid scheme (one of http(s)/ftp(s)/file/news/gopher)
 * @return boolean Success
 */
    public static function url($check, $strict = false) {
        self::_populateIp();
        $validChars = '([' . preg_quote('!"$&\'()*+,-.@_:;=~[]') . '\/0-9a-z\p{L}\p{N}]|(%[0-9a-f]{2}))';
        $regex = '/^(?:(?:https?|ftps?|sftp|file|news|gopher):\/\/)' . (!empty($strict) ? '' : '?') .
            '(?:' . self::$_pattern['IPv4'] . '|\[' . self::$_pattern['IPv6'] . '\]|' . self::$_pattern['hostname'] . ')(?::[1-9][0-9]{0,4})?' .
            '(?:\/?|\/' . $validChars . '*)?' .
            '(?:\?' . $validChars . '*)?' .
            '(?:#' . $validChars . '*)?$/iu';
        return self::_check($check, $regex);
    }

/**
 * Checks if a value is in a given list.
 *
 * @param string $check Value to check
 * @param array $list List to check against
 * @param boolean $strict Defaults to true, set to false to disable strict type check
 * @return boolean Success
 */
    public static function inList($check, $list, $strict = true) {
        return in_array($check, $list, $strict);
    }

/**
 * Runs an user-defined validation.
 *
 * @param string|array $check value that will be validated in user-defined methods.
 * @param object $object class that holds validation method
 * @param string $method class method name for validation to run
 * @param array $args arguments to send to method
 * @return mixed user-defined class class method returns
 */
    public static function userDefined($check, $object, $method, $args = null) {
        return call_user_func_array(array($object, $method), array($check, $args));
    }

/**
 * Checks that a value is a valid uuid - http://tools.ietf.org/html/rfc4122
 *
 * @param string $check Value to check
 * @return boolean Success
 */
    public static function uuid($check) {
        $regex = '/^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[1-5][a-fA-F0-9]{3}-[89aAbB][a-fA-F0-9]{3}-[a-fA-F0-9]{12}$/';
        return self::_check($check, $regex);
    }

/**
 * Attempts to pass unhandled Validation locales to a class starting with $classPrefix
 * and ending with Validation. For example $classPrefix = 'nl', the class would be
 * `NlValidation`.
 *
 * @param string $method The method to call on the other class.
 * @param mixed $check The value to check or an array of parameters for the method to be called.
 * @param string $classPrefix The prefix for the class to do the validation.
 * @return mixed Return of Passed method, false on failure
 */
    protected static function _pass($method, $check, $classPrefix) {
        $className = ucwords($classPrefix) . 'Validation';
        if (!class_exists($className)) {
            trigger_error(__d('cake_dev', 'Could not find %s class, unable to complete validation.', $className), E_USER_WARNING);
            return false;
        }
        if (!method_exists($className, $method)) {
            trigger_error(__d('cake_dev', 'Method %s does not exist on %s unable to complete validation.', $method, $className), E_USER_WARNING);
            return false;
        }
        $check = (array)$check;
        return call_user_func_array(array($className, $method), $check);
    }

/**
 * Runs a regular expression match.
 *
 * @param string $check Value to check against the $regex expression
 * @param string $regex Regular expression
 * @return boolean Success of match
 */
    protected static function _check($check, $regex) {
        if (is_string($regex) && preg_match($regex, $check)) {
            return true;
        }
        return false;
    }

/**
 * Get the values to use when value sent to validation method is
 * an array.
 *
 * @param array $params Parameters sent to validation method
 * @return void
 */
    protected static function _defaults($params) {
        self::_reset();
        $defaults = array(
            'check' => null,
            'regex' => null,
            'country' => null,
            'deep' => false,
            'type' => null
        );
        $params = array_merge($defaults, $params);
        if ($params['country'] !== null) {
            $params['country'] = mb_strtolower($params['country']);
        }
        return $params;
    }

/**
 * Lazily populate the IP address patterns used for validations
 *
 * @return void
 */
    protected static function _populateIp() {
        if (!isset(self::$_pattern['IPv6'])) {
            $pattern = '((([0-9A-Fa-f]{1,4}:){7}(([0-9A-Fa-f]{1,4})|:))|(([0-9A-Fa-f]{1,4}:){6}';
            $pattern .= '(:|((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})';
            $pattern .= '|(:[0-9A-Fa-f]{1,4})))|(([0-9A-Fa-f]{1,4}:){5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})';
            $pattern .= '(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)';
            $pattern .= '{4}(:[0-9A-Fa-f]{1,4}){0,1}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2}))';
            $pattern .= '{3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){0,2}';
            $pattern .= '((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|';
            $pattern .= '((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}';
            $pattern .= '((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2}))';
            $pattern .= '{3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)(:[0-9A-Fa-f]{1,4})';
            $pattern .= '{0,4}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)';
            $pattern .= '|((:[0-9A-Fa-f]{1,4}){1,2})))|(:(:[0-9A-Fa-f]{1,4}){0,5}((:((25[0-5]|2[0-4]';
            $pattern .= '\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4})';
            $pattern .= '{1,2})))|(((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?';

            self::$_pattern['IPv6'] = $pattern;
        }
        if (!isset(self::$_pattern['IPv4'])) {
            $pattern = '(?:(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])';
            self::$_pattern['IPv4'] = $pattern;
        }
    }

/**
 * Reset internal variables for another validation run.
 *
 * @return void
 */
    protected static function _reset() {
        self::$errors = array();
    }
}
