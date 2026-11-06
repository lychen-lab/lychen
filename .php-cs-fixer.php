<?php

// The one PHP code style of the monorepo, as eslint.config.js is for the
// frontends. Every PHP project's format and format-fix tasks
// (.moon/tasks/php.yml) check their own share of these files, and the editor
// formats with it on save (.vscode/settings.json), all through
// .moon/scripts/php-cs-fixer.sh. Never override the rules on the command line.
//
// The finder takes every PHP file of the PHP projects, as the editor formats
// any file it saves: what CI checks is what the editor writes.
//
// What is left out is listed here rather than read from .gitignore
// (ignoreVCSIgnored): Symfony's Finder only reads the root .gitignore when .git
// is a directory, which it is in CI but not in a git worktree, and the root
// .gitignore ignores public/*, where the APIs' index.php is tracked.
$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/libs/php',
        __DIR__.'/projects/*/api',
    ])
    // Matched by name at any depth, as vendor/ is by default: the APIs' QA
    // tools and their vendor/, and Symfony's cache. Never add `packages` here,
    // it would also drop config/packages/.
    ->exclude(['tools', 'var'])
    // Relative to each directory above: an API's packages/, a copy of libs/php
    // (copy-packages), and its secrets vault (secrets:set), where the prod
    // decryption key lives.
    ->notPath(['#^packages/#', '#^config/secrets/#']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
