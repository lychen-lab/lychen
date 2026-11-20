<?php

namespace App\Tests\Utils\Trait;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

trait IdCollisionTrait
{
    /**
     * Gives the next row of $entityClass's table the id of $entity, a row of another table: an API
     * key the id of a person, to prove that nothing reads the one id as the other. Each table has
     * its own id sequence, so such collisions happen in production; in a test they would only
     * happen by chance.
     *
     * Moves the table's id sequence, which a rollback does not restore: call it before the test
     * creates a row in that table, so that the id is still free.
     *
     * @param class-string $entityClass
     */
    protected function giveNextRowTheIdOf(string $entityClass, object $entity): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $id = $entityManager->getClassMetadata($entity::class)->getIdentifierValues($entity)['id'] ?? null;

        if (!is_int($id)) {
            throw new \LogicException(sprintf('%s has no integer id yet: persist it first.', get_debug_type($entity)));
        }

        $entityManager->getConnection()->executeQuery(
            'SELECT setval(pg_get_serial_sequence(:table, \'id\'), :id, false)',
            ['table' => $entityManager->getClassMetadata($entityClass)->getTableName(), 'id' => $id],
            ['table' => ParameterType::STRING, 'id' => ParameterType::INTEGER],
        );
    }
}
