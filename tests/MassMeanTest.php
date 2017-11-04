<?php

namespace IgnasiMG\Tests\BigDataUtils;

use IgnasiMG\BigDataUtils\MassMean;
use PHPUnit\Framework\TestCase;

class MassMeanTest extends TestCase
{
    public function testMeanIsCorrect()
    {
        $sut = new MassMean();

        $sut->AddValue(1);
        $sut->AddValue(2);
        $sut->AddValue(3);

        self::assertSame(2.0, $sut->getMean());
    }
}
