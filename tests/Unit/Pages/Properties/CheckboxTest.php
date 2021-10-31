<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\Checkbox;
use Notion\Pages\Properties\Factory;
use PHPUnit\Framework\TestCase;

class CheckboxTest extends TestCase
{
    public function test_create(): void
    {
        $checkbox = Checkbox::create(true);

        $this->assertTrue($checkbox->property()->isCheckbox());
        $this->assertTrue($checkbox->isChecked());
    }

    public function test_check(): void
    {
        $checkbox = Checkbox::create()->check();

        $this->assertTrue($checkbox->isChecked());
    }

    public function test_uncheck(): void
    {
        $checkbox = Checkbox::create(true)->uncheck();

        $this->assertFalse($checkbox->isChecked());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "checkbox",
            "checkbox" => true,
        ];

        $checkbox = Checkbox::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $checkbox->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
