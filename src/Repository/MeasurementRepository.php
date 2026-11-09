<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\Measurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Measurement>
 */
class MeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Measurement::class);
    }

    /**
     * @return Measurement[]
     */
    public function findByLocation(Location $location): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.location = :location')
            ->setParameter('location', $location)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Measurement[]
     */
    public function findAllOrderedByDate(): array
    {
        return $this->findBy([], ['date' => 'DESC', 'id' => 'DESC']);
    }
}
