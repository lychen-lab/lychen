<?php

namespace App\Api\Transform;

use App\Doctrine\UuidEntityFinder;
use Lychen\UtilModel\Interface\UuidIdentifiedInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Object mapper transform resolving the uuid(s) a client sends for a relation into the
 * entities to link.
 *
 * Input DTO -> entity direction, on the properties of a POST or PATCH DTO:
 *
 *     #[UuidExists(SeedLibrary::class)]
 *     #[Map(transform: UuidToSeedLibrary::class)]
 *     public Uuid $seedLibrary;
 *
 * - a Uuid becomes the entity: the mapper hands it to the setter (setSeedLibrary());
 * - a list of Uuid becomes the list of entities: the mapper syncs the collection with it
 *   through the adder and remover (addCharacteristic() / removeCharacteristic()), so
 *   `[]` unlinks them all.
 *
 * Unknown uuids are rejected earlier, by #[UuidExists] on the DTO: reaching one here
 * means the DTO lacks that constraint.
 *
 * A subclass per related entity names its class: a transform service gets the value,
 * not the property it is mapped to.
 *
 * @template T of UuidIdentifiedInterface
 *
 * @implements TransformCallableInterface<object, object>
 */
abstract class AbstractUuidToEntity implements TransformCallableInterface
{
    public function __construct(private readonly UuidEntityFinder $finder)
    {
    }

    /**
     * @return class-string<T>
     */
    abstract protected function entityClass(): string;

    /**
     * @return T|list<T>|null
     */
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Uuid) {
            return $this->resolve([$value])[0];
        }

        if (\is_array($value)) {
            return $this->resolve(array_values($value));
        }

        throw new \InvalidArgumentException(\sprintf('Expected a Uuid or a list of Uuid, got "%s".', get_debug_type($value)));
    }

    /**
     * @param list<mixed> $values
     *
     * @return list<T>
     */
    private function resolve(array $values): array
    {
        $uuids = [];
        foreach ($values as $value) {
            if (!$value instanceof Uuid) {
                throw new \InvalidArgumentException(\sprintf('Expected a Uuid, got "%s".', get_debug_type($value)));
            }
            $uuids[] = $value;
        }

        $found = $this->finder->findByUuids($this->entityClass(), $uuids);

        $entities = [];
        foreach ($uuids as $uuid) {
            $entities[] = $found[$uuid->toRfc4122()] ?? throw new \UnexpectedValueException(\sprintf('No "%s" matches the UUID "%s": check it with #[UuidExists] on the input DTO.', $this->entityClass(), $uuid->toRfc4122()));
        }

        return $entities;
    }
}
