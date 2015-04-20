<?php

namespace Inneair\Synapps\Cache;

/**
 * A cache implementation which stores entries in memory (available along the script execution), and, optionally, is
 * able to use a slower second-level cache to persist efficiently entries. This implementation does not support tags.
 */
class MemoryCache implements CacheInterface
{
    /**
     * If this cache (and the optional second-level cache) is enabled.
     * @var bool
     */
    protected $enabled;
    /**
     * Optional second-level cache.
     * @var CacheInterface
     */
    protected $nestedCache;
    /**
     * An array of entries, indexed by their ID.
     * @var array
     */
    protected $entries;

    /**
     * Builds a cache using the given parameter.
     *
     * @param bool $enabled If cache is enabled (defaults to <code>true</code>).
     * @param CacheInterface $nestedCache Optional second-level cache (defaults to <code>null</code>).
     */
    public function __construct($enabled = true, CacheInterface $nestedCache = null)
    {
        $this->enabled = $enabled;
        $this->nestedCache = $nestedCache;
        $this->entries = array();
    }

    /**
     * This implementation does not support the <code>mode</code> parameter and tagging, but they are provided to the
     * second-level cache, if used.
     * {@inheritdoc}
     */
    public function clear($mode = null, array $tags = null)
    {
        if ($this->enabled) {
            unset($this->entries);
            $this->entries = array();
            if ($this->nestedCache === null) {
                $cleared = true;
            } else {
                $cleared = $this->nestedCache->clear($mode, $tags);
            }
        } else {
            $cleared = true;
        }

        return $cleared;
    }

    /**
     * Tells whether there is an entry with the given ID and if it is still alive.
     *
     * @param string $id Entry ID.
     * @return bool If there is an entry with this ID, and it is alive.
     */
    public function hasEntry($id)
    {
        return array_key_exists($id, $this->entries);
    }

    /**
     * Tells whether this cache is enabled.
     *
     * @return bool If this cache is enabled.
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function load($id)
    {
        if ($this->enabled) {
            if ($this->hasEntry($id)) {
                $entry = $this->entries[$id];
            } elseif ($this->nestedCache === null) {
                $entry = false;
            } else {
                $entry = $this->nestedCache->load($id);
                if ($entry !== false) {
                    // Store the entry from the second-level cache into this first-level cache.
                    $this->entries[$id] = $entry;
                }
            }
        } else {
            $entry = false;
        }

        return $entry;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        if ($this->enabled) {
            if ($this->hasEntry($id)) {
                unset($this->entries[$id]);
            }
            if ($this->nestedCache === null) {
                $removed = true;
            } else {
                $removed = $this->nestedCache->remove($id);
            }
        } else {
            $removed = true;
        }

        return $removed;
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, array $tags = null)
    {
        if ($this->enabled) {
            $this->entries[$id] = $data;
            if ($this->nestedCache === null) {
                $saved = true;
            } else {
                $saved = $this->nestedCache->save($id, $data, $tags);
            }
        } else {
            $saved = true;
        }

        return $saved;
    }

    /**
     * Enable or disable the cache.
     *
     * @param bool $enabled If the cache must be enabled.
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}
