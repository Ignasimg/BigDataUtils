<?php

namespace IgnasiMG\Tests\BigDataUtils;

use IgnasiMG\BigDataUtils\MassMean;
use PHPUnit\Framework\TestCase;

class MassMeanTest extends TestCase
{
    /**
     * @dataProvider meanProvider
     */
    public function testMeanIsCorrect(float $expectedMean, array $values)
    {
        $sut = new MassMean();

        foreach ($values as $value) {
            $sut->AddValue($value);
        }

        self::assertSame($expectedMean, $sut->getMean());
    }

    public function meanProvider(): array
    {
        return [
            'simplest case' => [ 1.0, [1] ],
            'simple case' => [ 2.0, [1,2,3] ]
        ];
    }
}
