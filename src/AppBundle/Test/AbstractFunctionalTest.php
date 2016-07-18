<?php
namespace AppBundle\Test;


use AppBundle\Domain\Users;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;

class AbstractFunctionalTest extends KernelTestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface;
     */
    protected $container;

    /**
     * Is creating/dropping DB required for this test?.
     *
     * @var bool
     */
    protected $usesDB = true;

    /**
     * Whether to drop DB in self::tearDown, will only be true if test DB was created successfully.
     *
     * @var bool
     */
    private $dropDB = false;

    /**
     * Doing default preparations, setting up database.
     */
    public function setUp()
    {
        parent::setUp();
        parent::bootKernel();
        $this->container = self::$kernel->getContainer();

        try {
            if ($this->usesDB) {
                //Creating test DB and migrating
                //Attention - in order for this to work "server_version" must be provided in the db config.
                $application = new Application(self::$kernel);
                $application->add(new CreateDatabaseDoctrineCommand());
                $executor = new CommandTester($application->find('doctrine:database:create'));
                $executor->execute(['command' => 'doctrine:database:create']);
                if ($executor->getStatusCode() !== 0) {
                    throw new \RuntimeException('Failed to create db. Command output:'.PHP_EOL.$executor->getDisplay());
                }
                $this->dropDB = true;
                $this->container->get('doctrine.dbal.default_connection')->close();
                gc_collect_cycles();

                $application = new Application(self::$kernel);
                $application->setAutoExit(false);
                $application->add(new MigrationsMigrateDoctrineCommand());
                $input = new ArrayInput(
                    array(
                        'command' => 'doctrine:migrations:migrate',
                    )
                );
                $input->setInteractive(false);
                $output = new BufferedOutput();
                if ($application->run($input, $output) !== 0) {
                    throw new \RuntimeException('Failed to migrate. Command output:'.PHP_EOL.$output->fetch());
                }
                $this->container->get('doctrine.dbal.default_connection')->close();
                gc_collect_cycles();
            }
        } catch (\Throwable $ex) {
            echo PHP_EOL.'Bootstrap error:'.PHP_EOL.$ex->getMessage().PHP_EOL;
            $this->container->get('doctrine.dbal.default_connection')->close();
            gc_collect_cycles();
            $this->markTestSkipped($ex->getMessage());
        }
    }

    /**
     * Cleaning test DB and doing default clean ups.
     */
    public function tearDown()
    {
        if ($this->usesDB && $this->dropDB) {
            try {
                //Dropping the DB
                $application = new Application(self::$kernel);
                $application->setAutoExit(false);
                $application->add(new DropDatabaseDoctrineCommand());
                $input = new ArrayInput(
                    array(
                        'command' => 'doctrine:database:drop',
                        '--force' => true,
                    )
                );
                $input->setInteractive(false);
                $output = new BufferedOutput();
                if ($application->run($input, $output) !== 0) {
                    throw new \RuntimeException('Failed to drop db '.$output->fetch());
                }
            } finally {
                $this->container->get('doctrine.dbal.default_connection')->close();
                gc_collect_cycles();
            }
        }
        parent::tearDown();
    }

    /**
     * @return EntityManager
     */
    protected function getDoctrine(): EntityManager
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * @return User
     */
    protected function createDummyUser(): User
    {
        /** @var Users $users */
        $users = $this->container->get('app.users');
        $user = new User();
        $user->setEmail('test'.uniqid().'@test.com');
        $user->setPassword('12345');
        $user->setNickname('Unique Nickname#'.uniqid());
        $users->create($user);

        return $user;
    }
}