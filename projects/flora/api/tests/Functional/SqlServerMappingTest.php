<?php

namespace App\Tests\Functional;

use App\Entity\SeedLot;
use App\SqlServer\Entity\PlantObservation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The two entity managers keep to their own entities: the default one (Postgres) never
 * maps src/SqlServer/Entity, or its schema check would want SQL Server's tables in
 * Postgres. Reads the metadata only, so it runs without a SQL Server, as in CI.
 */
class SqlServerMappingTest extends KernelTestCase
{
    public function testEachEntityHasTheManagerOfItsDatabase(): void
    {
        $doctrine = self::getContainer()->get('doctrine');
        self::assertInstanceOf(ManagerRegistry::class, $doctrine);

        self::assertSame($doctrine->getManager('sqlserver'), $doctrine->getManagerForClass(PlantObservation::class));
        self::assertSame($doctrine->getManager('default'), $doctrine->getManagerForClass(SeedLot::class));

        self::assertSame([PlantObservation::class], $this->mappedClasses($doctrine, 'sqlserver'));
        self::assertNotContains(PlantObservation::class, $this->mappedClasses($doctrine, 'default'));
    }

    public function testTheSqlServerMappingIsValid(): void
    {
        $manager = self::getContainer()->get('doctrine')->getManager('sqlserver');
        self::assertInstanceOf(EntityManagerInterface::class, $manager);

        self::assertSame([], (new SchemaValidator($manager))->validateMapping());
    }

    /**
     * @return list<string>
     */
    private function mappedClasses(ManagerRegistry $doctrine, string $manager): array
    {
        return array_map(
            static fn ($metadata): string => $metadata->getName(),
            $doctrine->getManager($manager)->getMetadataFactory()->getAllMetadata(),
        );
    }
}
