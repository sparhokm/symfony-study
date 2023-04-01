<?php

use PhpCsFixerCustomFixers\Fixer\MultilinePromotedPropertiesFixer;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->append([__FILE__])
    ->exclude([
        'var',
        'vendor',
        'public',
        'docker',
    ])
;

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/var/cache/.php_cs')
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PHP82Migration' => true,
        '@PHPUnit84Migration:risky' => true,

        'yoda_style' => false,

        'concat_space' => ['spacing' => 'one'],
        'cast_spaces' => ['space' => 'none'],

        'global_namespace_import' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],

        'phpdoc_to_comment' => false,
        'phpdoc_separation' => false,
        'phpdoc_align' => false,

        'php_unit_strict' => false,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],

        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments']],

        'final_class' => true,
        'final_public_method_for_abstract_class' => true,
        'self_static_accessor' => true,

        'static_lambda' => true,

        MultilinePromotedPropertiesFixer::name() => ['minimum_number_of_parameters' => 2],
    ])
    ->setFinder($finder)
;
