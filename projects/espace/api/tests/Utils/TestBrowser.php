<?php

namespace App\Tests\Utils;

use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\KernelBrowser;

class TestBrowser extends KernelBrowser
{
    public function patch(string $url, $options = ['json' => []]): self
    {
        $this->setDefaultHttpOptions(
            HttpOptions::create()->withHeader('Content-Type', 'application/merge-patch+json')->withHeader('Accept', 'application/ld+json')
        );

        return parent::patch($url, $options);
    }
}
