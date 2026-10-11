<?php

// The one PHP code style of this API, read as is by CI (`ci-php-cs-fixer`) and
// locally (`moon <api>:php-cs-fixer-fix`): never override the rules or paths on
// the command line. Named .php-cs-fixer.php rather than .dist.php because
// .dockerignore drops *.dist.php and CI runs inside the test image.
$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__.'/src', __DIR__.'/tests']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
