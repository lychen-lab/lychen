<?php

namespace App\Tests\Functional;

use App\Entity\SeedLibrary;
use Lychen\Api\Interface\UuidIdentifiedInterface;
use Lychen\Api\UuidContract;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * lychen/api checks its uuid contract by shape: flora's entities implement lychen/model's
 * UuidIdentifiedInterface, not lychen/api's, and still fulfill it.
 */
class UuidContractTest extends TestCase
{
    public function testAnEntityImplementingLychenModelsInterfaceFulfillsIt(): void
    {
        self::assertFalse((new \ReflectionClass(SeedLibrary::class))->implementsInterface(UuidIdentifiedInterface::class));
        self::assertTrue(UuidContract::isFulfilledBy(SeedLibrary::class));

        $library = new SeedLibrary();
        self::assertSame($library->getUuid(), UuidContract::uuidOf($library));
    }

    public function testAnyClassWithAUuidGetterFulfillsIt(): void
    {
        $uuid = Uuid::v7();
        $entity = new class($uuid) {
            public function __construct(private readonly Uuid $uuid)
            {
            }

            public function getUuid(): Uuid
            {
                return $this->uuid;
            }
        };

        self::assertTrue(UuidContract::isFulfilledBy($entity::class));
        self::assertSame($uuid, UuidContract::uuidOf($entity));
    }

    public function testAClassWithoutUuidDoesNot(): void
    {
        self::assertFalse(UuidContract::isFulfilledBy(\stdClass::class));

        $this->expectException(\LogicException::class);
        UuidContract::uuidOf(new \stdClass());
    }
}
