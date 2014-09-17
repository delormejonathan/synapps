<?php

namespace Inneair\Synapps\Test\Sql;

use Inneair\Synapps\Sql\Helper;
use Inneair\Synapps\Test\AbstractSynappsTest;

/**
 * Class containing test suite for the SQL helper class.
 */
class HelperTest extends AbstractSynappsTest
{
    /**
     * A pattern to be escaped.
     * @var string
     */
    const PATTERN = 'a';

    /**
     * Escape an empty string.
     */
    public function testEscapeEmptyPattern()
    {
        $this->assertEmpty(Helper::escapeLikePattern(null));
    }

    /**
     * Escape a valid pattern.
     */
    public function testEscapeValidPattern()
    {
        $this->assertEquals(self::PATTERN, Helper::escapeLikePattern(self::PATTERN));
    }

    /**
     * Escape the '%' wildcard character.
     */
    public function testEscapeAnyStringWildcardPattern()
    {
        $this->assertEquals('\\%', Helper::escapeLikePattern(Helper::LIKE_ANY_STRING_WILDCARD));
    }

    /**
     * Escape the '_' wildcard character.
     */
    public function testEscapeAnyCharWildcardPattern()
    {
        $this->assertEquals('\\_', Helper::escapeLikePattern(Helper::LIKE_ANY_CHAR_WILDCARD));
    }

    /**
     * Escape the default '\' escape character.
     */
    public function testEscapeEscapeCharacterPattern()
    {
        $this->assertEquals('\\\\', Helper::escapeLikePattern(Helper::LIKE_DEFAULT_ESCAPE_CHAR));
    }
}
