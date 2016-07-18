<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Controlling transactions performed via Doctrine ORM.
 */
class DoctrineTransactionControl extends AbstractTransactionControl
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    protected function doBegin()
    {
        $this->em->beginTransaction();
    }

    protected function doRollback()
    {
        for ($n = $this->em->getConnection()->getTransactionNestingLevel(); $n > 0; $n--) {
            $this->em->getConnection()->rollBack();
        }
    }

    protected function doCommit()
    {
        $this->em->commit();
    }
} 