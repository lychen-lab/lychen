<?php

namespace App\Validator;

use Lychen\UtilModel\Interface\UuidIdentifiedInterface;
use Symfony\Component\Validator\Constraint;

/**
 * The uuid (or each uuid of a list) must identify an existing entity.
 *
 * Put it on the relation properties of an input DTO: API Platform validates the DTO before
 * the object mapper resolves the uuids into entities (UuidToEntity), so every unknown uuid
 * of the request ends up as a 422 violation on its property (`seedLibrary`,
 * `characteristics[1]`).
 *
 * The entity is the target of the Doctrine association the property is mapped onto (see
 * MappedAssociationResolver). Name it with `entity` on a DTO that is not mapped onto one.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class UuidExists extends Constraint
{
    public const NOT_FOUND_ERROR = 'b1f7d8a4-3c5e-4a9b-8f21-6d0e7c4a2b93';

    public const MESSAGE = 'The UUID "{{ uuid }}" does not match any {{ entity }}.';

    protected const ERROR_NAMES = [
        self::NOT_FOUND_ERROR => 'NOT_FOUND_ERROR',
    ];

    public string $message = self::MESSAGE;

    /**
     * @param class-string<UuidIdentifiedInterface>|null $entity the entity the uuids identify, by default the one the property is mapped onto
     * @param string[]|null                              $groups
     */
    public function __construct(
        public ?string $entity = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
