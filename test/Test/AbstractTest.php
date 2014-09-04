<?php

namespace Inneair\Synapps\Test;

use Exception;
use PHPUnit_Framework_TestCase;

/**
 * Base class for all PHPUnit tests, providing a cleaning method which ensures test environment is reset correctly
 * between each test case.
 *
 * @author Innéair
 */
abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * A message that shall be used in assertions, when an exception did not occur.
     * @var string
     */
    const ASSERT_EXPECTED_EXCEPTION_MESSAGE = 'Expected exception did not occur.';

    /**
     * Absolute path of the data directory.
     * @var string
     */
    private $dataPath;
    /**
     * Absolute path prefix of the data directory, ending with a directory separator.
     * @var string
     */
    private $dataPathPrefix;

    public function __construct()
    {
        $this->dataPath = __DIR__ . '/../../data';
        $this->dataPathPrefix = $this->dataPath . DIRECTORY_SEPARATOR;
    }

    /**
     * Initializes environment for this test execution.
     */
    public function setUp()
    {
        // Clean this test environment
        $this->clean();
    }

    /**
     * Releases environment resources allocated by this test.
     */
    public function tearDown()
    {
        // Clean this test environment
        $this->clean();
    }

    /**
     * Cleans the environment before/after a unitary test execution.
     * 
     * This method may be overriden by concrete tests, to perform additional cleaning tasks before/after the execution.
     * This method is called after the setup is done, and before the teardown is done, in the base class. Sub-classes
     * shall always call this parent method before any statements.
     */
    protected function clean()
    {
    }

    /**
     * Gets the absolute path to the data directory, at the library's root.
     *
     * @return string Path.
     */
    protected function getDataPath()
    {
        return $this->dataPath;
    }

    /**
     * Gets the absolute path to the data directory, at the library's root, for use as a path prefix. The prefix ends
     * with a directory separator, so as it can be used immediately.
     *
     * @return string Path ending with a directory separator, useful to prefix a file name.
     */
    protected function getDataPathPrefix()
    {
        return $this->dataPathPrefix;
    }

    /**
     * Asserts an exception occured, by testing a flag, and, if not, fails with an explicit message.
     *
     * @param bool $hasException If an exception occured.
     */
    protected function assertException($hasException)
    {
        $this->assertTrue($hasException, static::ASSERT_EXPECTED_EXCEPTION_MESSAGE);
    }
}
