<?php

namespace Notion\Blocks\Renderer;

use Notion\Blocks\BlockInterface;

interface BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string;
}
