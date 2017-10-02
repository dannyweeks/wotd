<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Word;
use Doctrine\ORM\EntityRepository;

class WordRepository extends EntityRepository
{
    /**
     * @return Word[]
     */
    public function findAll()
    {
        return $this->findBy([], ['word' => 'ASC']);
    }
}
