<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'vendor',
        'tpl_c',
        'stacc',
    ])
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

$config = (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => true,
        'blank_line_before_statement' => true,
        'class_attributes_separation' => [
            'elements' => ['method' => 'one'],
        ],
        //'concat_space' => ['spacing' => 'one'],
        'elseif' => true,
        'function_typehint_space' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => ['author', 'category', 'copyright', 'package', 'subpackage', 'version'],
        ],
        'global_namespace_import' => [
            'import_constants' => true,
            'import_functions' => true,
        ],
        'heredoc_to_nowdoc' => true,
        'list_syntax' => ['syntax' => 'short'],
        'lowercase_cast' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'native_function_type_declaration_casing' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_null_property_initialization' => true,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
        ],
        'normalize_index_brace' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha'
        ],
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => [
            'replacements' => [
                'type' => 'var',
                'link' => 'see',
            ],
        ],
        'phpdoc_scalar' => true,
        'phpdoc_separation' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'short_scalar_cast' => true,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'single_quote' => true,
        'standardize_increment' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'visibility_required' => [
            'elements' => ['property', 'method'],
        ],
        'whitespace_after_comma_in_array' => true,
    ])
    ->setFinder($finder);

return $config;
