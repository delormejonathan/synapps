<?php

namespace Inneair\Synapps\Util;

/**
 * Class containing helper functions for string management and conversions.
 */
final class StringUtils
{
    /**
     * The empty string constant.
     * @var string
     */
    const EMPTY_STR = '';
    /**
     * A string containing the <code>null</code> value.
     * @var string
     */
    const NULL_STR = 'null';
    /**
     * The quote character.
     * @var string
     */
    const QUOTE = '\'';
    /**
     * The double-quote character.
     * @var string
     */
    const DOUBLE_QUOTE = '"';
    /**
     * A default separator used when imploding arrays.
     * @var string
     */
    const ARRAY_VALUES_SEPARATOR = ',';
    /**
     * The opening square bracket character.
     * @var string
     */
    const OPEN_SQUARE_BRACKET = '[';
    /**
     * The closing square bracket character.
     * @var string
     */
    const CLOSE_SQUARE_BRACKET = ']';

    /**
     * Empty private constructor to prevent erroneous instantiations.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Checks if two strings are the same.
     *
     * This function does not support multi-bytes strings yet.
     *
     * @param string $str1 First string.
     * @param string $str2 Second string.
     * @param boolean $ignoreCase If the comparison must be done with a case-insensitive way (defaults to
     * <code>false</code>).
     * @return boolean <code>true</code> if both strings are the same.
     */
    public static function equals($str1, $str2, $ignoreCase = false)
    {
        $comparator = ($ignoreCase) ? 'strcasecmp' : 'strcmp';
        return ($comparator($str1, $str2) === 0);
    }

    /**
     * Returns a default variable if the passed variable is <code>null</code>.
     *
     * This function returns either the passed variable <code>var</code>, or if the variable is <code>null</code>, the
     * value of the <code>defaultVar</code> variable. If the passed variable is not <code>null</code>, it may be
     * enclosed between the given <code>quote</code> parameter, depending on its type:
     * - if it is a string and the <code>quote</code> is <code>null</code>, it will be enclosed between single quotes.
     * - if it is an array and the <code>quote</code> is <code>null</code>, it will be enclosed between square brackets.
     *
     * @param mixed $var The variable to check.
     * @param mixed $defaultVar The default variable to return, if the variable to check is <code>null</code> (defaults
     * to the <code>NULL_STR</code> string).
     * @param mixed $quote The variable used to quote the variable returned.
     * @return mixed The passed in variable, optionnaly enclosed between the given quotes if a string, or the default
     * one if it was <code>null</code>.
     */
    public static function defaultString($var, $defaultVar = self::NULL_STR, $quote = null)
    {
        $result = null;
        if ($var === null) {
            $result = $defaultVar;
        } elseif (is_string($var)) {
            if ($quote === null) {
                $quote = static::QUOTE;
            }
            $result = $quote . $var . $quote;
        } elseif (is_array($var)) {
            if ($quote === null) {
                $quoteStart = static::OPEN_SQUARE_BRACKET;
                $quoteEnd = static::CLOSE_SQUARE_BRACKET;
            } else {
                $quoteStart = $quote;
                $quoteEnd = $quote;
            }
            $result = $quoteStart . static::implodeRecursively($var, static::ARRAY_VALUES_SEPARATOR, $quote, true)
                . $quoteEnd;
        } else {
            $result = $var;
        }

        return $result;
    }

    /**
     * Recursively implodes an array.
     *
     * This method is a 'recursive' version of the PHP 'implode' method, with keys dump capability, and data types
     * management. When an inner array is found, it is imploded too, and its values can be enclosed between a
     * <code>quote</code> value. This method concats all values with the glue. When keys are shown, values are prefixed
     * with the key and the equal sign. If <code>quote</code> is omitted, square brackets are used for inner arrays.
     *
     * @param array $pieces See {@link implode}.
     * @param string $glue See {@link implode}.
     * @param mixed $quote A quote used to enclose inner arrays (defaults to <code>null</code>, i.e. square brackets
     * will be used).
     * @param boolean $showKeys If array keys must be shown (defaults to <code>false</code>).
     * @return string See {@link implode} function.
     */
    public static function implodeRecursively(
        array $pieces,
        $glue = self::EMPTY_STR,
        $quote = null,
        $showKeys = false
    ) {
        $result = '';
        foreach ($pieces as $key => &$value) {
            if (!empty($result)) {
                $result .= $glue;
            }

            if ($showKeys) {
                $result .= static::defaultString($key) .'=';
            }
            if (is_array($value)) {
                if ($quote === null) {
                    $quoteStart = static::OPEN_SQUARE_BRACKET;
                    $quoteEnd = static::CLOSE_SQUARE_BRACKET;
                } else {
                    $quoteStart = $quote;
                    $quoteEnd = $quote;
                }
                $result .= $quoteStart . static::implodeRecursively($value, $glue, $quote) . $quoteEnd;
            } else {
                $result .= static::defaultString($value);
            }
        }

        return $result;
    }

    /**
     * Tells whether a string is blank or not.
     *
     * A string is blank:
     * - if it is <code>null</code>.
     * - if it equals the empty string, after triming whitespace characters (space, tab, line feed, carriage return,
     * NUL byte, or vertical tab).
     *
     * @param string $string A string.
     * @return bool <code>true</code> if the string is blank, <code>false</code> otherwise.
     */
    public static function isBlank($string)
    {
        return (($string === null) || (is_string($string) && (trim($string) === static::EMPTY_STR)));
    }
}
