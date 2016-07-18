<?php
namespace AppBundle\Repository\Doctrine;


use AppBundle\Entity\User;
use AppBundle\Repository\EntityCollectionInterface;
use AppBundle\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }

    public function update(User $user)
    {
        $this->_em->flush($user);
    }

    public function findByCriteria(string $email = null): EntityCollectionInterface
    {
        $query = $this->createQueryBuilder('u');
        $query->select();
        if ($email) {
            $query->andWhere($query->expr()->eq('u.email', ':email'))->setParameter('email', $email);
        }

        return new EntityCollection($query);
    }
}