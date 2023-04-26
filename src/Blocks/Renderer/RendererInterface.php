<?php

namespace Notion\Blocks\Renderer;

use Notion\Blocks\BlockInterface;

interface RendererInterface
{
    public static function render(BlockInterface ...$blocks): string;

    public static function renderBlock(BlockInterface $block, int $depth = 0): string;
}
