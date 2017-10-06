<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadUsersData implements FixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $objectManager)
    {
        /** @var UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $adminUser */
        $adminUser = $userManager->createUser();

        $adminUser
            ->setUsername('admin')
            ->setEmail('admin@example.com')
            ->setPlainPassword('admin')
            ->setEnabled(true)
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setFirstName('Leeroy')
            ->setLastName('Jenkins');

        /** @var User $user */
        $user = $userManager->createUser();

        $user
            ->setUsername('user')
            ->setEmail('user@example.com')
            ->setPlainPassword('user')
            ->setEnabled(true)
            ->setRoles(['ROLE_USER'])
            ->setFirstName('Joe')
            ->setLastName('Schmoe');

        $userManager->updateUser($adminUser);
        $userManager->updateUser($user);

        for ($i = 0; $i < 5; $i++) {
            $userManager->updateUser($this->getRandomUser());
        }

        // You can flush by passing `true` as the second argument to `updateUser()`
        // But I feel like it's more evident if we do it explicitly.
        $objectManager->flush();
    }

    /**
     * @return UserInterface
     */
    protected function getRandomUser()
    {
        $names = [
            'James',
            'John',
            'Robert',
            'Michael',
            'William',
            'David',
            'Richard',
            'Charles',
            'Joseph',
            'Thomas',
            'Christopher',
            'Daniel'
        ];

        $firstname = $names[rand(0, count($names) - 1)];
        $surname = $names[rand(0, count($names) - 1)];

        return $this->container
            ->get('fos_user.user_manager')
            ->createUser()
            ->setUsername($firstname . $surname)
            ->setEmail($firstname . $surname. '@example.com')
            ->setPlainPassword('user')
            ->setEnabled(true)
            ->setRoles(['ROLE_USER'])
            ->setFirstName($firstname)
            ->setLastName($surname);
    }
}
