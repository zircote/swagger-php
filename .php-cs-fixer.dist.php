<?php declare(strict_types=1);

use OpenApi\Tools\CSFixer\ScopedLicenseFixer;
use OpenApi\Tools\CSFixer\ScopedDeclareStrictTypesFixer;

$finder = PhpCsFixer\Finder::create()
    ->path('src')->name('*.php')
    ->path('tests')->name('*.php')
        // ContextTest::testFullyQualifiedName relies on the 'use Exception' statement...
        ->filter(function (\SplFileInfo $file) {
            return !strpos($file->getPathname(), 'tests/Fixtures/Customer.php');
        })
    ->path('Examples')->name('*.php')
        ->filter(function (\SplFileInfo $file) {
            return !strpos($file->getPathname(), 'Examples/petstore-3.0/Petstore.php')
                && !strpos($file->getPathname(), 'Examples/misc/OpenApiSpec.php');
        })
    ->path('tools')->name('*.php')
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->registerCustomFixers([
        (new ScopedLicenseFixer())->scope(['/src/', '/tests/']), //, '/Examples/']),
        (new ScopedDeclareStrictTypesFixer())->scope(['/src/', '/tests/']),
    ])
    ->setRules([
        '@PSR2' => true,
        '@DoctrineAnnotation' => true,
        'OpenApi/license' => true,
        'OpenApi/declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'blank_line_before_statement' => ['statements' => ['return']],
        'visibility_required' => true,
        'cast_spaces' => ['space' => 'single'],
        'concat_space' => ['spacing' => 'one'],
        'function_typehint_space' => true,
        'lowercase_cast' => true,
        'magic_constant_casing' => true,
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'single_blank_line_before_namespace' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'single_quote' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_extra_blank_lines' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'short_scalar_cast' => true,
        'space_after_semicolon' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => true,
        'trim_array_spaces' => true,
        'single_space_after_construct' => true,
        'single_line_comment_spacing' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => null, 'import_functions' => null],

        'no_empty_phpdoc' => true,
        // 7.3 only 'no_superfluous_phpdoc_tags' => true,
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
        'phpdoc_separation' => ['skip_unlisted_annotations' => true],
        'phpdoc_scalar' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_alias_tag' => true,
    ])
    ->setFinder($finder)
;
