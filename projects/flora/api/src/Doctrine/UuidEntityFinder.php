<?php

namespace App\Doctrine;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Lychen\UtilModel\Interface\UuidIdentifiedInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Finds entities by their API identifier: the uuid clients send to link resources.
 *
 * One query per call, whatever the number of uuids. The uuids are bound as RFC 4122
 * strings, which PostgreSQL compares with its native UUID column.
 */
final readonly class UuidEntityFinder
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    /**
     * @template T of UuidIdentifiedInterface
     *
     * @param class-string<T> $class
     * @param list<Uuid>      $uuids
     *
     * @return array<string, T> the entities found, indexed by their RFC 4122 uuid
     */
    public function findByUuids(string $class, array $uuids): array
    {
        if ([] === $uuids) {
            return [];
        }

        $manager = $this->registry->getManagerForClass($class);
        if (!$manager instanceof EntityManagerInterface) {
            throw new \LogicException(\sprintf('"%s" is not a Doctrine ORM entity.', $class));
        }

        /** @var list<T> $entities */
        $entities = $manager->createQueryBuilder()
            ->select('e')
            ->from($class, 'e')
            ->where('e.uuid IN (:uuids)')
            ->setParameter(
                'uuids',
                array_map(static fn (Uuid $uuid): string => $uuid->toRfc4122(), $uuids),
                ArrayParameterType::STRING,
            )
            ->getQuery()
            ->getResult();

        $found = [];
        foreach ($entities as $entity) {
            $found[$entity->getUuid()->toRfc4122()] = $entity;
        }

        return $found;
    }
}
