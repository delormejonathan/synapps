<?php

namespace Inneair\Synapps\Test\Util;

use Inneair\Synapps\Test\AbstractSynappsTest;
use Inneair\Synapps\Security\UUID;

/**
 * Class containing test suite for the <code>UUID</code> class.
 */
class UUIDTest extends AbstractSynappsTest
{
    /**
     * Generates a UUID.
     */
    public function testUuid()
    {
        $uuid = UUID::randomUuid();
        $this->assertNotNull($uuid);
        $this->assertTrue(mb_ereg_match(UUID::REGEX_PATTERN, $uuid));
    }
}
