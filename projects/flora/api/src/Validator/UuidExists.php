<?php

namespace App\Validator;

use Lychen\UtilModel\Interface\UuidIdentifiedInterface;
use Symfony\Component\Validator\Constraint;

/**
 * The uuid (or each uuid of a list) must identify an existing $entity.
 *
 * Put it on the input DTOs' relation properties: API Platform validates the DTO before
 * the object mapper resolves the uuids into entities, so an unknown uuid ends up as a
 * 422 violation on its property (`seedLibrary`, `characteristics[1]`...).
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class UuidExists extends Constraint
{
    public const NOT_FOUND_ERROR = 'b1f7d8a4-3c5e-4a9b-8f21-6d0e7c4a2b93';

    protected const ERROR_NAMES = [
        self::NOT_FOUND_ERROR => 'NOT_FOUND_ERROR',
    ];

    public string $message = 'The UUID "{{ uuid }}" does not match any {{ entity }}.';

    /**
     * @param class-string<UuidIdentifiedInterface> $entity
     * @param string[]|null                         $groups
     */
    public function __construct(
        public string $entity,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
