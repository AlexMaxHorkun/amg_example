<?php
namespace AppBundle\Repository;

/**
 * Repositories may return this object to delegate loading of entities and provide common filters.
 */
interface EntityCollectionInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param int|null $limit
     * @return EntityCollectionInterface
     */
    public function setLimit(int $limit = null): EntityCollectionInterface;

    /**
     * @return int|null
     */
    public function getLimit();

    /**
     * @param int $offset
     * @return EntityCollectionInterface
     */
    public function setOffset(int $offset): EntityCollectionInterface;

    /**
     * @return int
     */
    public function getOffset(): int;

    /**
     * Load entities and return them.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * First element found.
     *
     * @return object|null
     */
    public function first();
}