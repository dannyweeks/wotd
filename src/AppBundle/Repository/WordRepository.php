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
    public function getAFreshWord()
    {
        // Create the base query
        $countQb = $this->createQueryBuilder('w');
        $countQb
            ->andWhere($countQb->expr()->isNull('w.date'))
            ->setMaxResults(1);

        // Clone the current query before we add the count constraint
        $qb = clone $countQb;

        $count = $countQb
            ->select('COUNT(w)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($count == 0) {
            return null;
        }

        // Using the offset fetch a random row
        $qb->setFirstResult(rand(0, $count - 1));

        return $qb->getQuery()->getOneOrNullResult();
    }
}
