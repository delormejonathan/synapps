<?php

namespace Inneair\Util;

use RuntimeException;

/**
 * This class provides utilities for regular expressions management.
 *
 * @author Innéair
 */
class RegexUtils
{
    /**
     * Escapes all special characters related to regular expression in a string.
     *
     * This is a simple version of the PHP 'preg_quote' supporting multi-byte strings.
     *
     * @param string $var The string to be escaped.
     * @return string The escaped string.
     * @throws RuntimeException If an error occurred while quoting the string.
     */
    public static function quote($var)
    {
        $newVar = mb_ereg_replace('([\^\$\(\)\[\]\|\.\*\+\?])', '\\\\1', $var);
        if ($newVar === false) {
            throw new RuntimeException('Cannot quote string \'' . $var . '\'');
        }
        return $newVar;
    }
}
