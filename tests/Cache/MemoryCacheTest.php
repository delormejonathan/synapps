<?php

namespace Inneair\Synapps\Test\Cache;

use Inneair\Synapps\Cache\CacheInterface;
use Inneair\Synapps\Cache\MemoryCache;
use Inneair\Synapps\Test\AbstractTest;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class containing test suite for memory cache.
 */
class MemoryCacheTest extends AbstractTest
{
    /**
     * An ID for a cache entry
     * @var string
     */
    const ENTRY_ID = 'id';
    /**
     * Data for a cache entry.
     * @var mixed
     */
    const ENTRY_DATA = 'data';

    /**
     * Optional second-level cache.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $nestedCache;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->nestedCache = $this->createMock(CacheInterface::class);
    }

    /**
     * Tests clearing the cache and its nested cache.
     */
    public function testClearNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('clear')->willReturn(true);
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertTrue($cache->clear());
    }

    /**
     * Tests clearing the cache fails if clearing the second-level cache fails.
     */
    public function testFailedClearFromNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('clear')->willReturn(false);
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertFalse($cache->clear());
    }

    /**
     * Tests removing data from the cache fails if removing data from the second-level cache fails.
     */
    public function testFailedRemoveFromNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('remove')->with(static::ENTRY_ID)->willReturn(false);
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertFalse($cache->remove(static::ENTRY_ID));
    }

    /**
     * Tests a caching data in the cache fails if caching data in the second-level cache fails.
     */
    public function testFailedSaveFromNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('save')->with(static::ENTRY_ID)->willReturn(false);
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertFalse($cache->save(static::ENTRY_ID, static::ENTRY_DATA));
    }

    /**
     * Tests loading returns the data if if exists only in the nested cache.
     */
    public function testLoadEntryWithNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('load')->with(static::ENTRY_ID)->willReturn(
            static::ENTRY_DATA
        );
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertSame(static::ENTRY_DATA, $cache->load(static::ENTRY_ID));
    }

    /**
     * Tests loading missing data with a nested cache returns false.
     */
    public function testLoadFromNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('load')->with(static::ENTRY_ID)->willReturn(
            static::ENTRY_DATA
        );
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertSame(static::ENTRY_DATA, $cache->load(static::ENTRY_ID));
        // This second attempt shall not query the nested cache. This is verified by the expectation at the top of this
        // method, which is based on one call of the nested cache 'load' method.
        $this->assertSame(static::ENTRY_DATA, $cache->load(static::ENTRY_ID));
    }

    /**
     * Tests loading missing data with a nested cache returns false.
     */
    public function testLoadMissingEntryFromNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('load')->with(static::ENTRY_ID)->willReturn(false);
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertFalse($cache->load(static::ENTRY_ID));
    }

    /**
     * Tests loading missing data without a nested cache returns false.
     */
    public function testLoadMissingEntryWithoutNestedCache()
    {
        $cache = new MemoryCache();
        $this->assertFalse($cache->load(static::ENTRY_ID));
    }

    /**
     * Tests default configuration of a new cache.
     */
    public function testNewCache()
    {
        $cache = new MemoryCache();
        $this->assertTrue($cache->isEnabled());
    }

    /**
     * Tests removing data from the cache and its nested cache.
     */
    public function testRemoveFromNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('remove')->with(static::ENTRY_ID)->willReturn(true);
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertTrue($cache->remove(static::ENTRY_ID));
    }

    /**
     * Tests caching data into the cache and its nested cache.
     */
    public function testSaveIntoNestedCache()
    {
        $this->nestedCache->expects(static::once())->method('save')->with(static::ENTRY_ID)->willReturn(true);
        $cache = new MemoryCache(true, $this->nestedCache);
        $this->assertTrue($cache->save(static::ENTRY_ID, static::ENTRY_DATA));
    }

    /**
     * Tests caching data and clearing the cache without a nested cache.
     */
    public function testSaveLoadClearWithoutNestedCache()
    {
        $cache = new MemoryCache();
        $this->assertTrue($cache->save(static::ENTRY_ID, static::ENTRY_DATA));
        $this->assertSame(static::ENTRY_DATA, $cache->load(static::ENTRY_ID));
        $this->assertTrue($cache->clear());
        $this->assertFalse($cache->load(static::ENTRY_ID));
    }

    /**
     * Tests saving data does nothing if the cache is disabled.
     */
    public function testSaveLoadRemoveClearWithDisabledCache()
    {
        $cache = new MemoryCache();
        $cache->setEnabled(false);
        $this->assertFalse($cache->isEnabled());
        $this->assertTrue($cache->save(static::ENTRY_ID, static::ENTRY_DATA));
        $this->assertFalse($cache->load(static::ENTRY_ID));
        $this->assertTrue($cache->remove(static::ENTRY_ID));
        $this->assertTrue($cache->clear());
    }

    /**
     * Tests caching data and removing it without a nested cache.
     */
    public function testSaveLoadRemoveWithoutNestedCache()
    {
        $cache = new MemoryCache();
        $this->assertTrue($cache->save(static::ENTRY_ID, static::ENTRY_DATA));
        $this->assertSame(static::ENTRY_DATA, $cache->load(static::ENTRY_ID));
        $this->assertTrue($cache->remove(static::ENTRY_ID));
        $this->assertFalse($cache->load(static::ENTRY_ID));
    }
}
