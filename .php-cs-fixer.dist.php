<?php declare(strict_types=1);

use OpenApi\Tools\CSFixer\ScopedDeclareStrictTypesFixer;
use OpenApi\Tools\CSFixer\ScopedLicenseFixer;
use OpenApi\Tools\CSFixer\SpecNamespaceAliasFixer;

$finder = PhpCsFixer\Finder::create()
    ->path('src')->name('*.php')
    ->path('tools')->name('*.php')
    ->path('docs/examples')->name('*.php')
    ->path('docs/snippets')->name('*.php')
    ->path('tests')->name('*.php')
    ->filter(function (\SplFileInfo $file) {
        return
            // ContextTest::testFullyQualifiedName relies on the 'use Exception' statement...
            !strpos($file->getPathname(), 'tests/Fixtures/Customer.php')
            // multi arg use; 'use a, b;`
            && !strpos($file->getPathname(), 'tests/Fixtures/Parser/HelloTrait.php')
            // FQDN in docblock
            && !strpos($file->getPathname(), 'tests/Fixtures/TypedProperties.php')
            // FQDN in docblock
            && !strpos($file->getPathname(), 'tests/Fixtures/PHP/DocblockAndTypehintTypes.php')
            // parameter docblock for PHP 8.6
            && !strpos($file->getPathname(), 'tests/Fixtures/Scratch/Docblocks.php')
            // FQDN in data provider
            && !strpos($file->getPathname(), 'tests/Utils/TokenScannerTest.php')
        ;
    })
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->registerCustomFixers([
        (new ScopedLicenseFixer())->scope(['/src/', '/tests/', '/docs/examples/']),
        (new ScopedDeclareStrictTypesFixer())->scope(['/src/', '/tests/']),
        (new SpecNamespaceAliasFixer())->scope(['/src/', '/tests/', '/docs/examples/']),
    ])
    ->setRules([
        '@PSR12' => true,
        '@DoctrineAnnotation' => true,
        'OpenApi/license' => true,
        'OpenApi/declare_strict_types' => true,
        'OpenApi/spec_namespace_alias' => true,
        'blank_line_after_opening_tag' => false,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'blank_line_before_statement' => ['statements' => ['return']],
        'modifier_keywords' => true,
        'cast_spaces' => ['space' => 'single'],
        'concat_space' => ['spacing' => 'one'],
        'type_declaration_spaces' => true,
        'lowercase_cast' => true,
        'magic_constant_casing' => true,
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'blank_lines_before_namespace' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'single_quote' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_extra_blank_lines' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'no_trailing_comma_in_singleline' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_braces' => true,
        'short_scalar_cast' => true,
        'space_after_semicolon' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => true,
        'trim_array_spaces' => true,
        'single_space_around_construct' => true,
        'single_line_comment_spacing' => true,
        'fully_qualified_strict_types' => ['import_symbols' => true, 'leading_backslash_in_global_namespace' => true],
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => null, 'import_functions' => null],

        'attribute_empty_parentheses' => [
                'use_parentheses' => false,
        ],
        'nullable_type_declaration_for_default_null_value' => true,

        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'phpdoc_align' => true,
        'general_phpdoc_tag_rename' => true,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_tag_type' => true,
        'phpdoc_indent' => true,
        'phpdoc_var_without_name' => true,
        'phpdoc_types' => true,
        'phpdoc_trim' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_summary' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_separation' => false,
        'phpdoc_scalar' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_param_order' => true,
        'multiline_comment_opening_closing' => true,
        'phpdoc_line_span' => ['class' => 'multi', 'method' => 'multi', 'property' => null, 'const' => null, 'case' => null, 'function' => null],

        'php_unit_attributes' => true,
    ])
    ->setFinder($finder);
