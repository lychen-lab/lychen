<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class LandProposalOnlyOneStatePerLand extends Constraint
{
    public string $message = 'You can only have one proposal in {{ state }} state.';

    public function __construct(
        public readonly array $states = [],
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);

        if (null !== $message) {
            $this->message = $message;
        }
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption(): ?string
    {
        return 'states';
    }
}
