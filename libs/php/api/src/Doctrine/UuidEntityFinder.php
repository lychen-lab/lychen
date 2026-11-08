<?php

namespace Lychen\Api\Doctrine;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Lychen\Api\UuidContract;
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
     * @template T of object
     *
     * @param class-string<T> $class
     * @param list<mixed>     $uuids instances of Uuid or uuid strings; anything else is never found
     *
     * @return array<string, T> the entities found, indexed by their key()
     */
    public function findByUuids(string $class, array $uuids): array
    {
        $keys = [];
        foreach ($uuids as $uuid) {
            if (null !== $key = self::key($uuid)) {
                $keys[$key] = $key;
            }
        }

        if ([] === $keys) {
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
            ->setParameter('uuids', array_values($keys), ArrayParameterType::STRING)
            ->getQuery()
            ->getResult();

        $found = [];
        foreach ($entities as $entity) {
            $found[UuidContract::uuidOf($entity)->toRfc4122()] = $entity;
        }

        return $found;
    }

    /**
     * The RFC 4122 form of a uuid sent by a client, the key of findByUuids()' result; null
     * when the value is not a uuid.
     */
    public static function key(mixed $uuid): ?string
    {
        if ($uuid instanceof Uuid) {
            return $uuid->toRfc4122();
        }

        if (\is_string($uuid) && Uuid::isValid($uuid)) {
            return Uuid::fromString($uuid)->toRfc4122();
        }

        return null;
    }
}
