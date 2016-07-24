<?php
// vim: syntax=php:

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\FixerInterface;

$finder = DefaultFinder::create()
    ->in(__DIR__ . '/benchmark')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return Config::create()
    ->level(FixerInterface::PSR2_LEVEL)
    ->fixers([
        'array_element_no_space_before_comma',
        'array_element_white_space_after_comma',
        'line_after_namespace',
        'new_with_braces',
        'no_blank_lines_before_namespace',
        'ordered_use',
        'return',
    ])
    ->finder($finder)
;
