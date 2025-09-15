<?php

declare(strict_types=1);

/*
 * PHP-CS-Fixer configuration for KraftDo NFC
 * Maintains consistent code style across the project
 */

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'bootstrap/cache',
        'storage',
        'vendor',
        'node_modules',
        'public/build',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        // PSR-12 compliance
        '@PSR12' => true,
        
        // Additional style rules
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'not_operator_with_successor_space' => true,
        'trailing_comma_in_multiline' => true,
        'phpdoc_scalar' => true,
        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'method_chaining_indentation' => true,
        'general_phpdoc_tag_rename' => true,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_tag_type' => true,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        
        // Laravel-specific rules
        'concat_space' => ['spacing' => 'one'],
        'new_with_braces' => true,
        'braces' => ['allow_single_line_closure' => true],
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'allow_unused_params' => true],
        'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'single_trait_insert_per_statement' => true,
        
        // Security and best practices
        'strict_comparison' => false, // Laravel uses loose comparisons in many places
        'strict_param' => false,
        'declare_strict_types' => false, // Not enforced in Laravel projects
        'mb_str_functions' => true,
        'native_function_invocation' => false, // Can cause issues with Laravel helpers
        'no_alias_functions' => true,
        'random_api_migration' => true,
        
        // Code organization
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'single_class_element_per_statement' => true,
        'visibility_required' => true,
        'return_type_declaration' => true,
        'no_useless_return' => true,
        
        // Comments and documentation
        'multiline_comment_opening_closing' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'comment_to_phpdoc' => true,
        
        // Array and object formatting
        'normalize_index_brace' => true,
        'whitespace_after_comma_in_array' => true,
        'trim_array_spaces' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');