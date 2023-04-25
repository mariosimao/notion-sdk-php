<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Code;
use Notion\Blocks\CodeLanguage;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\CodeRenderer;
use PHPUnit\Framework\TestCase;

class CodeRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Code::fromString("<?php\n\necho 'Hello world!';", CodeLanguage::Php);

        $markdown = CodeRenderer::render($block);

        $expected = <<<MARKDOWN
```php
<?php

echo 'Hello world!';
```
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }


    public function test_invalid_block(): void
    {
        $markdown = CodeRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
