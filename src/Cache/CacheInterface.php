<?php

namespace Inneair\Synapps\Cache;

/**
 * This interface provides the services a cache shall implement.
 */
interface CacheInterface
{
    /**
     * Clears the cache so as it is empty.
     *
     * @param mixed $mode Clear mode. Its up to the implementation to provide details about available modes.
     * @param string[] $tags An array of tags. Only entries having the given tags will be cleared, depending on the
     * selected mode (defaults to <code>null</code>).
     * @return bool <code>true</code> if the cache was cleared, <code>false</code> otherwise.
     */
    public function clear($mode, array $tags = null);

    /**
     * Removes an entry using its ID.
     *
     * @param string $id Entry ID.
     * @return bool <code>true</code> if the entry was removed, <code>false</code> otherwise.
     */
    public function remove($id);

    /**
     * Loads an entry with its ID.
     *
     * @param string $id Entry ID.
     * @return mixed The entry, or <code>false</code> if no entry was found.
     */
    public function load($id);

    /**
     * Saves or updates an entry in the cache.
     *
     * @param string $id Entry ID.
     * @param mixed $data The data to be saved.
     * @param string[] $tags Tags related to the entry (defaults to <code>null</code>).
     * @return bool <code>true</code> if the entry was saved, <code>false</code> otherwise.
     */
    public function save($id, $data, array $tags = null);
}
