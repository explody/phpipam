<?php

$header = <<<'EOF'
This file is part of phpipam 

Copyright (c) Miha Petkovsek <miha.petkovsek@gmail.com>
              and the phpipam project contributors

EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => false,
        'array_syntax' => array('syntax' => 'long'),
        'combine_consecutive_unsets' => true,
        // one should use PHPUnit methods to set up expected exception instead of annotations
        'general_phpdoc_annotation_remove' => array('expectedException', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp'),
        'header_comment' => array('header' => $header),
        'heredoc_to_nowdoc' => true,
        'no_extra_consecutive_blank_lines' => array('break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block'),
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => false,
        'ordered_imports' => true,
        'php_unit_strict' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'psr4' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'concat_space' => array('spacing' => 'one'),
    ))
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(['tests/Fixtures','vendor','functions/adLDAP'])
            ->in(__DIR__)
    )
;