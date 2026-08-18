<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\BankResource;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

/**
 * PR-08: BankResource contract remains id/name/code/sort_number (unchanged).
 *
 * Pure PHPUnit — no Laravel app / DB / Redis bootstrap.
 */
class BankResourceContractTest extends TestCase
{
    public function test_to_array_returns_expected_fields_only(): void
    {
        $bank = new class
        {
            public int $id = 7;

            public string $name = 'Saudi National Bank';

            public string $code = 'SNB';

            public int $sort_number = 3;

            public string $fees = '9.99';

            public int $active = 1;
        };

        $array = (new BankResource($bank))->toArray(Request::create('/'));

        $this->assertSame([
            'id' => 7,
            'name' => 'Saudi National Bank',
            'code' => 'SNB',
            'sort_number' => 3,
        ], $array);
        $this->assertArrayNotHasKey('fees', $array);
        $this->assertArrayNotHasKey('active', $array);
    }
}
