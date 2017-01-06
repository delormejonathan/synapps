<?php

namespace Inneair\Synapps\Test\Security;

use Inneair\Synapps\Test\AbstractSynappsTest;
use Inneair\Synapps\Security\Uuid;

/**
 * Class containing test suite for the <code>Uuid</code> class.
 */
class UuidTest extends AbstractSynappsTest
{
    /**
     * Generates a UUID.
     */
    public function testUuid()
    {
        $uuid = Uuid::randomUuid();
        $this->assertNotNull($uuid);
        $this->assertTrue(mb_ereg_match(Uuid::REGEX_PATTERN, (string) $uuid));
    }
}
