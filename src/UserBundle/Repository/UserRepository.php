<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findLikeEmail($str)
    {
        $qb = $this->createQueryBuilder('u');
        return $qb->where(
                $qb->expr()->like('u.email', ':user')
            )
            ->setParameter('user','%' . $str . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByAccount($account)
    {
        $q = $this->createQueryBuilder('u')
            ->where('u.account = :account')
            ->setParameter('account', $account)
            ->getQuery();

        return $q->getResult();

    }
}
