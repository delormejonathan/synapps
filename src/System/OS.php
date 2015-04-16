<?php

namespace Inneair\Synapps\System;

/**
 * This class encapsulates OS information.
 */
class OS
{
    /**
     * Instance matching the current OS executing PHP.
     * @var OS
     */
    private static $instance = null;

    /**
     * Name of the operating system.
     * @var string
     */
    private $name;
    /**
     * Whether this OS is a Windows one.
     * @var bool
     */
    private $windows;
    /**
     * Whether this OS is Mac OS.
     * @var bool
     */
    private $macintosh;

    /**
     * Prevents outer instantiation. Only the inner factory has control on instantiation.
     */
    private function __construct()
    {
        $this->name = PHP_OS;
        $this->windows = (mb_strtolower(mb_substr($this->name, 0, 3)) === 'win');
        $this->macintosh = (mb_strtolower($this->name) === 'darwin');
    }

    /**
     * Gets the singleton instance of this class, i.e. the current OS executing PHP.
     *
     * @return OS The singleton instance of this class.
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Tells whether the current OS is a Windows OS.
     *
     * @return bool <code>true</code> if this a Windows OS, <code>false</code> otherwise.
     */
    public function isWindows()
    {
        return $this->windows;
    }

    /**
     * Tells whether the current OS is a Mac OS.
     *
     * @return bool <code>true</code> if this a Macintosh OS, <code>false</code> otherwise.
     */
    public function isMacintosh()
    {
        return $this->macintosh;
    }
}
