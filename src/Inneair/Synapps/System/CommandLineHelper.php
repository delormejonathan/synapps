<?php

namespace Inneair\Synapps\System;

/**
 * This class provides utilities to interact with the command line of a process.
 */
class CommandLineHelper
{
    /**
     * Parses command line to extract options.
     *
     * Example:
     * <code>$ php test.php plain-arg --foo --bar=baz --funny="spam=eggs" --alsofunny=spam=eggs \
     * > 'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
     *
     * $out = array(12) {
     *   [0]           => string(9) "plain-arg"
     *   ["foo"]       => bool(true)
     *   ["bar"]       => string(3) "baz"
     *   ["funny"]     => string(9) "spam=eggs"
     *   ["alsofunny"] => string(9) "spam=eggs"
     *   [1]           => string(11) "plain arg 2"
     *   ["a"]         => bool(true)
     *   ["b"]         => bool(true)
     *   ["c"]         => bool(true)
     *   ["k"]         => string(5) "value"
     *   [2]           => string(11) "plain arg 3"
     *   ["s"]         => string(9) "overwrite"
     * }</code>
     *
     * @param string[] $argv Array containing command line arguments to be parsed.
     * @return array
     */
    public static function parseArgs($argv)
    {
        // Shift the batch name
        array_shift($argv);
        $out = array();

        $plainArgIndex = 0;
        foreach ($argv as &$arg) {
            // Arguments format: --foo --bar=baz
            if (substr($arg, 0, 2) === '--') {
                $eqPos = strpos($arg, '=');
                if ($eqPos === false) {
                    // --foo
                    $key = substr($arg, 2);
                    $value = isset($out[$key]) ? $out[$key] : true;
                } else {
                    // --bar=baz
                    $key = substr($arg, 2, $eqPos - 2);
                    $value = substr($arg, $eqPos + 1);
                }
            } elseif (substr($arg, 0, 1) === '-') {
                // Arguments format: -k=value -abc
                if (substr($arg, 2, 1) === '=') {
                    // -k=value
                    $key = substr($arg, 1, 1);
                    $value = substr($arg, 3);
                } else {
                    // -abc
                    $chars = str_split(substr($arg, 1));
                    foreach ($chars as $char) {
                        $key = $char;
                        $value = isset($out[$key]) ? $out[$key] : true;
                    }
                }
            } else {
                // plain-arg
                $value = $arg;
                $key = $plainArgIndex;
                $plainArgIndex++;
            }

            if (strstr($value, ',')) {
                $value = explode(',', $value);
            }
            $out[$key] = $value;
        }

        return $out;
    }
}
