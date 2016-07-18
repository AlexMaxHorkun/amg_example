<?php
namespace AppBundle\Domain;

use AppBundle\Service\AbstractTransactional;

/**
 * having dependency on transaction control.
 */
trait TransactionalTrait
{
    /**
     * @var AbstractTransactional
     */
    protected $transactional;

    /**
     * @param AbstractTransactional $transactional
     */
    public function setTransactional(AbstractTransactional $transactional)
    {
        $this->transactional = $transactional;
    }
}