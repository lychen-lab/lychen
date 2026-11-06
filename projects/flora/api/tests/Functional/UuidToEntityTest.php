<?php

namespace App\Tests\Functional;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Api\Transform\UuidToEntity;
use App\Doctrine\MappedAssociationResolver;
use App\Doctrine\UuidEntityFinder;
use App\Entity\Characteristic;
use App\Entity\SeedLibrary;
use App\Entity\SeedLot;
use App\Factory\CharacteristicFactory;
use App\Factory\SeedLibraryFactory;
use App\Validator\UuidExists;
use Doctrine\Persistence\ManagerRegistry;
use Lychen\Test\AbstractApiTestCase;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;

/**
 * UuidToEntity on its own, with input DTOs shaped to reach its edge cases. The target is
 * a SeedLot: its associations tell the transform which entity to load.
 */
class UuidToEntityTest extends AbstractApiTestCase
{
    public function testAnEmptyListIsNotMistakenForAnotherEmptyList(): void
    {
        $dto = new class {
            /** @var list<string> */
            public array $tags = [];

            /** @var list<Uuid> */
            #[Map(transform: UuidToEntity::class)]
            public array $characteristics = [];
        };

        self::assertSame([], $this->transform()($dto->characteristics, $dto, new SeedLot()));
    }

    public function testResolvesTheEntitiesOfTheMappedAssociationInOrder(): void
    {
        $early = CharacteristicFactory::createOne();
        $hardy = CharacteristicFactory::createOne();
        $dto = new class {
            /** @var list<Uuid> */
            public array $characteristics = [];
        };
        $dto->characteristics = [$hardy->getUuid(), $early->getUuid()];

        $entities = $this->transform()($dto->characteristics, $dto, new SeedLot());

        self::assertIsArray($entities);
        self::assertCount(2, $entities);
        self::assertContainsOnlyInstancesOf(Characteristic::class, $entities);
        self::assertTrue($hardy->getUuid()->equals($entities[0]->getUuid()));
        self::assertTrue($early->getUuid()->equals($entities[1]->getUuid()));
    }

    public function testFollowsAPropertyRenamedByMap(): void
    {
        $library = SeedLibraryFactory::createOne();
        $dto = new class {
            #[Map(target: 'seedLibrary', transform: UuidToEntity::class)]
            public ?Uuid $library = null;
        };
        $dto->library = $library->getUuid();

        $entity = $this->transform()($dto->library, $dto, new SeedLot());

        self::assertInstanceOf(SeedLibrary::class, $entity);
        self::assertTrue($library->getUuid()->equals($entity->getUuid()));
    }

    public function testRefusesToGuessBetweenPropertiesHoldingTheSameValue(): void
    {
        $uuid = (string) Uuid::v7();
        $dto = new class {
            public ?string $seedLibrary = null;
            public ?string $previousSeedLibrary = null;
        };
        $dto->seedLibrary = $uuid;
        $dto->previousSeedLibrary = $uuid;

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('"seedLibrary", "previousSeedLibrary" hold the same value');

        $this->transform()($dto->seedLibrary, $dto, new SeedLot());
    }

    public function testAnUnknownUuidIsAViolationOnItsIndex(): void
    {
        $known = CharacteristicFactory::createOne();
        $dto = new class {
            /** @var list<Uuid> */
            public array $characteristics = [];
        };
        $dto->characteristics = [$known->getUuid(), Uuid::v7()];

        try {
            $this->transform()($dto->characteristics, $dto, new SeedLot());
            self::fail('An unknown uuid must throw a ValidationException.');
        } catch (ValidationException $exception) {
            $violations = $exception->getConstraintViolationList();
            self::assertCount(1, $violations);
            self::assertSame('characteristics[1]', $violations->get(0)->getPropertyPath());
            self::assertSame(UuidExists::NOT_FOUND_ERROR, $violations->get(0)->getCode());
        }
    }

    private function transform(): UuidToEntity
    {
        $doctrine = static::getContainer()->get('doctrine');
        self::assertInstanceOf(ManagerRegistry::class, $doctrine);

        return new UuidToEntity(new MappedAssociationResolver($doctrine), new UuidEntityFinder($doctrine));
    }
}
