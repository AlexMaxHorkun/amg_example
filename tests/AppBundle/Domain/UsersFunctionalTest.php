<?php
namespace AppBundle\Domain;


use AppBundle\Entity\User;
use AppBundle\Test\AbstractFunctionalTest;

class UsersFunctionalTest extends AbstractFunctionalTest
{
    private function getUsers(): Users
    {
        return $this->container->get('app.users');
    }

    public function testCreate()
    {
        $users = $this->getUsers();
        $doctrine = $this->getDoctrine();

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setPassword('12345');
        $user->setNickname('Unique Nickname');
        $users->create($user);
        $this->assertNotNull($user->getId());
        $doctrine->clear(User::class);
        /** @var User $user */
        $this->assertNotNull($user = $doctrine->find(User::class, $user->getId()));
        $this->assertFalse($user->isEmailConfirmed());
        $this->assertNotNull($user->getEmailConfirmCode());
    }

    public function testConfirmEmail()
    {
        $users = $this->getUsers();
        $doctrine = $this->getDoctrine();

        $user = $this->createDummyUser();
        try {
            $users->confirmEmail($user->getEmail(), 'fake code');
            $this->fail('Accepted fake code');
        } catch (\InvalidArgumentException $ex) {
            //Invalid code not accepted
        }
        $users->confirmEmail($user->getEmail(), $user->getEmailConfirmCode());
        $this->assertTrue($user->isEmailConfirmed());
        $this->assertNull($user->getEmailConfirmCode());
        $doctrine->clear();
        /** @var User $user */
        $user = $doctrine->find(User::class, $user->getId());
        $this->assertTrue($user->isEmailConfirmed());
        $this->assertNull($user->getEmailConfirmCode());
    }
}