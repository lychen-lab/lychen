<?php

namespace App\Entity\Interface;

use App\Entity\Land;

interface StateLandInterface
{
    public function getId(): ?int;

    public function getLand(): ?Land;

    public function getState(): ?string;
}
