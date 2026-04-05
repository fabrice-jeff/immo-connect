<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return Property[]
     */
    public function searchCatalog(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.owner', 'o')
            ->addSelect('c', 'o');

        $query = trim((string) ($filters['q'] ?? ''));
        if ($query !== '') {
            $qb
                ->andWhere('LOWER(p.title) LIKE :query OR LOWER(p.description) LIKE :query OR LOWER(p.location) LIKE :query OR LOWER(c.title) LIKE :query')
                ->setParameter('query', '%' . strtolower($query) . '%');
        }

        $category = trim((string) ($filters['category'] ?? ''));
        if ($category !== '') {
            $qb
                ->andWhere('c.title = :category')
                ->setParameter('category', $category);
        }

        $operation = trim((string) ($filters['operation'] ?? ''));
        if ($operation !== '') {
            $qb
                ->andWhere('p.operation = :operation')
                ->setParameter('operation', $operation);
        }

        $rooms = (int) ($filters['rooms'] ?? 0);
        if ($rooms > 0) {
            $qb
                ->andWhere('p.roomsNumber >= :rooms')
                ->setParameter('rooms', $rooms);
        }

        $maxPrice = (float) ($filters['max_price'] ?? 0);
        if ($maxPrice > 0) {
            $qb
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }

        $sort = $filters['sort'] ?? 'recent';
        match ($sort) {
            'price_asc' => $qb->orderBy('p.price', 'ASC'),
            'price_desc' => $qb->orderBy('p.price', 'DESC'),
            'surface_desc' => $qb->orderBy('p.surface', 'DESC'),
            default => $qb->orderBy('p.createdAt', 'DESC'),
        };

        return $qb->getQuery()->getResult();
    }
}
