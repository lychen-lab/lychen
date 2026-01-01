<?php

namespace Lychen\UtilModel\Interface;

use Symfony\Component\Uid\Uuid;

interface UuidIdentifiedInterface
{
    public function getUuid(): Uuid;

    public function setUuid(Uuid $uuid): self;
}
