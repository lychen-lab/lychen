<?php

namespace App\Entity\Interface;

use App\Entity\Person;

interface StatePersonInterface
{
    public function getId(): ?int;

    public function getPerson(): ?Person;

    public function setPerson(?Person $person): static;

    public function getState(): ?string;
}
