<?php

namespace Tests\Unit;

use App\Rules\NoTemplateSyntax;
use PHPUnit\Framework\TestCase;

class NoTemplateSyntaxTest extends TestCase
{
    public function testRejectsVueTemplateDelimiters()
    {
        $rule = new NoTemplateSyntax();

        $this->assertFalse($rule->passes('reference_id', '{{$randomInt 100 900000}}'));
        $this->assertFalse($rule->passes('items.0.name', 'Labtop {{ 1 + 1 }}'));
        $this->assertFalse($rule->passes('customer_notes', 'note }}'));
    }

    public function testAllowsOrdinaryText()
    {
        $rule = new NoTemplateSyntax();

        $this->assertTrue($rule->passes('reference_id', 'INV-100'));
        $this->assertTrue($rule->passes('items.0.name', 'Labtop HP'));
        $this->assertTrue($rule->passes('customer_notes', null));
        $this->assertTrue($rule->passes('tags', ''));
    }
}
