<?php
// vim: syntax=php:

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/benchmark')
    ->in(__DIR__ . '/tests')
;

return Config::create()
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_before_return' => false,
        'cast_spaces' => false,
        'concat_space' => ['spacing' => 'one'],
        'linebreak_after_opening_tag' => true,
        'no_blank_lines_before_namespace' => true,
        'no_php4_constructor' => true,
        'no_short_echo_tag' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'psr0' => true,
        'semicolon_after_instruction' => true,
        'single_blank_line_before_namespace' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
