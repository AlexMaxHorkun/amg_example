<?php
namespace AppBundle\Service;

/**
 * Manages transactions of a service (DB, cache, mq etc).
 */
interface TransactionControlInterface
{
    /**
     * Starts a new transaction.
     * If a transaction already started should do nothing.
     */
    public function begin();

    /**
     * Commits transaction.
     * If there is more then one transactions started should do nothing.
     */
    public function commit();

    /**
     * Undo the transaction.
     */
    public function rollback();
}