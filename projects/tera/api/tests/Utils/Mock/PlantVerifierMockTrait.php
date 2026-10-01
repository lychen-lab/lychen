<?php

namespace App\Tests\Utils\Mock;

use App\Service\PlantVerifier;
use PHPUnit\Framework\MockObject\MockObject;

trait PlantVerifierMockTrait
{
    protected function getMockedPlantVerifier(): MockObject
    {
        return $this->createMock(PlantVerifier::class);
    }

    protected function preventAssertPlantExistsException(MockObject $mock): MockObject
    {
        $mock->expects($this->any())->method('assertPlantExists')->willReturnCallback(function () {
        });

        return $mock;
    }
}
