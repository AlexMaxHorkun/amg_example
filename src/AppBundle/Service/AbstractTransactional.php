<?php
namespace AppBundle\Service;

/**
 * Provides "transactional" method for convenience.
 */
abstract class AbstractTransactional implements TransactionControlInterface
{
    /**
     * Given actions (function) will be executed within a single transaction.
     *
     * @param callable $actions
     * @throws \Throwable
     * @return mixed Result of $actions call.
     */
    public function transactional(callable $actions)
    {
        $this->begin();
        try {
            $result = $actions();
            $this->commit();
        } catch (\Throwable $ex) {
            $this->rollback();
            throw $ex;
        }

        return $result;
    }
} 