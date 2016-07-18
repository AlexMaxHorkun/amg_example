<?php
namespace AppBundle\Repository;

use AppBundle\Entity\User;

/**
 * Working with data storage used for users.
 */
interface UserRepositoryInterface
{
    /**
     * Insert user record, set generated ID.
     *
     * @param User $user
     */
    public function create(User $user);

    /**
     * Updates user record.
     *
     * @param User $user
     */
    public function update(User $user);

    /**
     * Find list of users by criteria.
     *
     * @param string|null $email
     * @return EntityCollectionInterface|User[]
     */
    public function findByCriteria(string $email = null): EntityCollectionInterface;
}