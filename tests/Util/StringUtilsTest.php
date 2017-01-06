<?php

namespace Inneair\Synapps\Test\Util;

use Inneair\Synapps\Test\AbstractSynappsTest;
use Inneair\Synapps\Util\StringUtils;

/**
 * Class containing test suite for string utilities.
 */
class StringUtilsTest extends AbstractSynappsTest
{
    /**
     * An input string.
     * @var string
     */
    const TEST_STRING = 'test';

    /**
     * Compares different strings.
     */
    public function testCompareDifferentStrings()
    {
        $this->assertFalse(StringUtils::equals(static::TEST_STRING, mb_strtoupper(static::TEST_STRING)));
    }

    /**
     * Compares strings with case insensitivity.
     */
    public function testCompareStringsIgnoreCase()
    {
        $this->assertTrue(StringUtils::equals(static::TEST_STRING, mb_strtoupper(static::TEST_STRING), true));
    }

    /**
     * Compares same strings.
     */
    public function testCompareSameStrings()
    {
        $this->assertTrue(StringUtils::equals(static::TEST_STRING, static::TEST_STRING));
    }

    /**
     * Gets the default string for an array.
     */
    public function testDefaultStringForArray()
    {
        $this->assertEquals(
            StringUtils::OPEN_SQUARE_BRACKET . StringUtils::CLOSE_SQUARE_BRACKET,
            StringUtils::defaultString(array())
        );
    }

    /**
     * Gets the default string for an array, with special quotes.
     */
    public function testDefaultStringForArrayWithQuotes()
    {
        $this->assertEquals(
            static::TEST_STRING . static::TEST_STRING,
            StringUtils::defaultString(array(), StringUtils::NULL_STR, static::TEST_STRING)
        );
    }

    /**
     * Gets the default string for an integer.
     */
    public function testDefaultStringForInteger()
    {
        $this->assertEquals(0, StringUtils::defaultString(0));
    }

    /**
     * Gets the default string for <code>null</code>.
     */
    public function testDefaultStringForNull()
    {
        $this->assertEquals(StringUtils::NULL_STR, StringUtils::defaultString(null));
    }

    /**
     * Gets the default string for a string.
     */
    public function testDefaultStringForString()
    {
        $this->assertEquals(
            StringUtils::QUOTE . static::TEST_STRING . StringUtils::QUOTE,
            StringUtils::defaultString(static::TEST_STRING)
        );
    }

    /**
     * Gets the default string for a string, with special quotes.
     */
    public function testDefaultStringForStringWithQuotes()
    {
        $this->assertEquals(
            static::TEST_STRING . static::TEST_STRING . static::TEST_STRING,
            StringUtils::defaultString(static::TEST_STRING, StringUtils::NULL_STR, static::TEST_STRING)
        );
    }

    /**
     * Implodes an array with a special glue.
     */
    public function testImplodeArrayWithGlue()
    {
        $this->assertEquals(
            StringUtils::QUOTE . static::TEST_STRING . StringUtils::QUOTE . StringUtils::ARRAY_VALUES_SEPARATOR
                . StringUtils::QUOTE . static::TEST_STRING . StringUtils::QUOTE,
            StringUtils::implodeRecursively(
                array(static::TEST_STRING, static::TEST_STRING),
                StringUtils::ARRAY_VALUES_SEPARATOR
            )
        );
    }

    /**
     * Implodes an array with an inner empty array.
     */
    public function testImplodeArrayWithInnerArray()
    {
        $this->assertEquals(
            StringUtils::OPEN_SQUARE_BRACKET . StringUtils::CLOSE_SQUARE_BRACKET,
            StringUtils::implodeRecursively(array(array()))
        );
    }

    /**
     * Implodes an array with an inner empty array, and special quotes.
     */
    public function testImplodeArrayWithInnerArrayAndQuotes()
    {
        $this->assertEquals(
            StringUtils::QUOTE . StringUtils::QUOTE,
            StringUtils::implodeRecursively(array(array()), StringUtils::EMPTY_STR, StringUtils::QUOTE)
        );
    }

    /**
     * Implodes an array and shows its keys.
     */
    public function testImplodeArrayWithKeys()
    {
        $this->assertEquals(
            StringUtils::QUOTE . static::TEST_STRING . StringUtils::QUOTE . '=' . StringUtils::QUOTE . static::TEST_STRING
                . StringUtils::QUOTE,
            StringUtils::implodeRecursively(
                array(static::TEST_STRING => static::TEST_STRING),
                StringUtils::EMPTY_STR,
                null,
                true
            )
        );
    }

    /**
     * Implodes an array with an inner <code>null</code> value.
     */
    public function testImplodeArrayWithNull()
    {
        $this->assertEquals(StringUtils::NULL_STR, StringUtils::implodeRecursively(array(null)));
    }

    /**
     * Implodes an empty array.
     */
    public function testImplodeEmptyArray()
    {
        $this->assertEquals(StringUtils::EMPTY_STR, StringUtils::implodeRecursively(array()));
    }

    /**
     * Tests if null is blank.
     */
    public function testIsNullBlank()
    {
        $this->assertTrue(StringUtils::isBlank(null));
    }

    /**
     * Tests if the empty string is blank.
     */
    public function testIsEmptyStringBlank()
    {
        $this->assertTrue(StringUtils::isBlank(StringUtils::EMPTY_STR));
    }

    /**
     * Tests if a string containing blank characters is blank.
     */
    public function testIsBlankStringBlank()
    {
        $this->assertTrue(StringUtils::isBlank(" \t\n\r\0\x0b"));
    }

    /**
     * Tests if a non-blank string is blank.
     */
    public function testIsStringBlank()
    {
        $this->assertFalse(StringUtils::isBlank(static::TEST_STRING));
    }
}
