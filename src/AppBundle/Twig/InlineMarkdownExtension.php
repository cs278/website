<?php

namespace AppBundle\Twig;

final class InlineMarkdownExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'inline_markdown';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('inline_markdown', function ($input) {
                // Emphasis
                $input = preg_replace_callback('{(\*{1,3})([^\*]+?)\1}', function ($m) {
                    $formats = [
                        1 => '<em>%s</em>',
                        2 => '<strong>%s</strong>',
                        3 => '<strong><em>%s</em></strong>',
                    ];

                    $format = $formats[strlen($m[1])];

                    return sprintf($format, $m[2]);
                }, $input);

                // Backticks
                $input = preg_replace_callback('{`([^`]+)`}', function ($m) {
                    return sprintf('<code>%s</code>', $m[1]);
                }, $input);

                // Anchors
                $input = preg_replace_callback('{\[([^\]]+)\]\(([^\)]+)\)}', function ($m) {
                    return sprintf('<a href="%s">%s</a>', $m[2], $m[1]);
                }, $input);

                return $input;
            }, ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }
}
