<?php
namespace AppBundle\Domain;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepositoryInterface;

/**
 * Managing users.
 */
class Users
{
    use TransactionalTrait;

    /**
     * @var UserRepositoryInterface
     */
    private $repo;

    /**
     * @param UserRepositoryInterface $repo
     */
    public function setRepo(UserRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Add new user to the system.
     *
     * @param User $user
     */
    public function create(User $user)
    {
        $this->transactional->transactional(
            function () use ($user) {
                $user->setEmailConfirmed(false);
                $user->setEmailConfirmCode(md5(uniqid()));
                $this->repo->create($user);
            }
        );
    }

    /**
     * Accept confirmation code in order for user to be able to pass authentication.
     *
     * @param string $email
     * @param string $code
     * @throws \InvalidArgumentException When email or code are not found.
     * @return User
     */
    public function confirmEmail(string $email, string $code): User
    {
        return $this->transactional->transactional(
            function () use ($email, $code) {
                /** @var User $user */
                $user = $this->repo->findByCriteria($email)->first();
                if (!$user) {
                    throw new \InvalidArgumentException('error.user_not_found');
                }
                if ($user->isEmailConfirmed()) {
                    throw new \RuntimeException('error.users_email_confirmed');
                }
                if ($user->getEmailConfirmCode() !== $code) {
                    throw new \InvalidArgumentException('error.invalid_code');
                }
                $user->setEmailConfirmed(true);
                $user->setEmailConfirmCode(null);
                $this->repo->update($user);

                return $user;
            }
        );
    }
}