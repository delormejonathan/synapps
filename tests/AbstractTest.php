<?php

namespace Inneair\Synapps\Test;

use PHPUnit\Framework\TestCase;

/**
 * Base class for all PHPUnit tests, providing a cleaning method which ensures test environment is reset correctly
 * between each test case.
 */
abstract class AbstractTest extends TestCase
{
    /**
     * A message that shall be used in assertions, when an exception did not occur.
     * @var string
     */
    const ASSERT_EXPECTED_EXCEPTION_MESSAGE = 'Expected exception did not occur.';

    /**
     * Initializes environment for this test execution.
     */
    public function setUp()
    {
        // Perform additional setup
        $this->afterSetUp();
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
     * Performs additional setup tasks, when needed, and cleans the environment.
     *
     * This method may be overriden by concrete tests, to perform additional tasks after the root setup.
     * This method is called after the setup is done, and before the cleaning is done, in the base class. Sub-classes
     * shall always call this parent method before any statements.
     */
    protected function afterSetUp()
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
     * Asserts an exception occured, by testing a flag, and, if not, fails with an explicit message.
     *
     * @param bool $hasException If an exception occured.
     */
    protected function assertException($hasException)
    {
        $this->assertTrue($hasException, static::ASSERT_EXPECTED_EXCEPTION_MESSAGE);
    }
}
