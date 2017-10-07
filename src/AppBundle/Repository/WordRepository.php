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

    /**
     * Get a random word that has not been assigned a date yet
     *
     * @return Word|null
     */
    public function getOneRandom()
    {
        $qb = $this->createQueryBuilder('w');

        $count = $qb
            ->select('COUNT(w)')
            ->where('w.date IS NULL')
            ->getQuery()
            ->getSingleScalarResult();

        if (!$count) {
            return null;
        }

        return $qb
            ->select('w')
            ->setFirstResult(rand(0, $count - 1))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
