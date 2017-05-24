<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->name('joomla-extractor')
    ->in(__DIR__.'/bin')
    ->in(__DIR__.'/src');

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules(
        [
            '@Symfony' => true,
            '@PHP71Migration' => true,
            'array_syntax' => ['syntax' => 'short'],
            'ordered_imports' => true,
            'phpdoc_order' => true,
            'return_type_declaration' => ['space_before' => 'none'],
        ]
    )
    ->setUsingCache(false);
