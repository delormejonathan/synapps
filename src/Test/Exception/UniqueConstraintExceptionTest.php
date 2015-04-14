<?php

namespace Inneair\Synapps\Test\IO;

use Inneair\Synapps\Exception\UniqueConstraintException;
use Inneair\Synapps\Test\AbstractSynappsTest;

/**
 * Class containing test suite for the {@link UniqueConstraintException} exception.
 */
class UniqueConstraintExceptionTest extends AbstractSynappsTest
{
    /**
     * A property.
     * @var string
     */
    const PROPERTY = 'property';

    /**
     * Throws an exception with a file path.
     */
    public function testProperty()
    {
        $hasException = false;
        try {
            throw new UniqueConstraintException(static::PROPERTY);
        } catch (UniqueConstraintException $e) {
            $this->assertEquals(static::PROPERTY, $e->getProperty());
            $hasException = true;
        }
        $this->assertException($hasException);
    }
}
