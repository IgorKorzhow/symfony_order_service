<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PSR2' => true,
        '@PSR1' => true,

        '@Symfony' => true,

        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],

        'psr_autoloading' => true,
    ])
    ->setFinder($finder);
