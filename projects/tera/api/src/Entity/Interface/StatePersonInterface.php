<?php

namespace App\Entity\Interface;

use App\Entity\Person;

interface StatePersonInterface
{
    public function getPerson(): ?Person;

    public function getState(): ?string;
}
