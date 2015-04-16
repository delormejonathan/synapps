<?php

namespace Inneair\Synapps\System;

/**
 * This class encapsulates runtime information, i.e. the environment in which PHP executes. It provides an interface to
 * interact with this environment. This class does not provide any information about PHP itself, see PhpRuntime class in
 * the same namespace.
 */
class Runtime
{
    /**
     * The runtime this application is running in.
     * @var Runtime
     */
    private static $instance = null;

    /**
     * Prevents outer instantiation. Only the inner factory has control on instantiation.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Gets the singleton instance of this class, i.e. the runtime this application is running in.
     *
     * @return Runtime The singleton instance of this class.
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Executes a command in another process.
     *
     * @param string $command The command to be executed.
     * @param bool $background If the command must be launched in background (defaults to <code>false</code>).
     * @param array $output See PHP exec command 'output' parameter (not used on Windows platform).
     * @param int|false $result Return status of the executed command, or <code>false</code> if the command cannot be
     * executed in background under Windows.
     */
    public function exec($command, $background = false, &$output = null, &$result = null)
    {
        $os = OS::getInstance();
        if ($background) {
            if ($os->isWindows()) {
                $command = 'start /B ' . $command . ' > NUL';
            } else {
                $command .= ' > /dev/null 2>&1 &';
            }
        }

        if ($background && $os->isWindows()) {
            $stdOut = popen($command, 'r');
            if ($stdOut === false) {
                $result = $stdOut;
            } else {
                $result = pclose($stdOut);
            }
        } else {
            exec($command, $output, $result);
        }
    }
}
