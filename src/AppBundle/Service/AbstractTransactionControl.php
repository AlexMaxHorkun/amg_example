<?php
namespace AppBundle\Service;

/**
 * Default implementation handling nested transactions.
 */
abstract class AbstractTransactionControl extends AbstractTransactional
{
    /**
     * @var int Number of active transactions.
     */
    protected $activeCount = 0;

    /**
     * Beginning new transaction only if there are no active transactions at the moment, else just counting one more.
     */
    public function begin()
    {
        if (!$this->activeCount) {
            $this->doBegin();
        }
        $this->activeCount++;
    }

    /**
     * Initiate new transaction.
     */
    abstract protected function doBegin();

    /**
     * Discarding transaction, if no transaction is active nothing will happen.
     */
    public function rollback()
    {
        if ($this->activeCount > 0) {
            $this->doRollback();
            $this->activeCount = 0;
        }
    }

    /**
     * Discard current transaction.
     */
    abstract protected function doRollback();

    /**
     * Committing only if it's the last 'commit' call.
     */
    public function commit()
    {
        if (!$this->activeCount) {
            throw new \RuntimeException('There are no active transactions to commit');
        }
        if ($this->activeCount > 1) {
            $this->activeCount--;
        } else {
            $this->activeCount = 0;
            $this->doCommit();
        }
    }

    /**
     * Commit current transaction.
     */
    abstract protected function doCommit();
}