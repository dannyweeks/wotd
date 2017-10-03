<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @return User[]
     */
    public function findAll()
    {
        return $this->findBy([], ['lastName' => 'ASC', 'firstName' => 'ASC']);
    }
}
