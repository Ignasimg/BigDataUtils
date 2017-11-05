<?php

namespace IgnasiMG\Tests\BigDataUtils;

use IgnasiMG\BigDataUtils\MassMean;
use PHPUnit\Framework\TestCase;

class MassMeanTest extends TestCase {
  /**
   * @dataProvider meanProvider
   */
  public function testMeanIsCorrect(float $expectedMean, array $add_values, array $remove_values) {
    $sut = new MassMean();

    foreach ($add_values as $value) {
      $sut->AddValue($value);
    }

    foreach ($remove_values as $value) {
      $sut->RemoveValue($value);
    }

    self::assertSame($expectedMean, $sut->getMean());
  }

  public function meanProvider(): array {
    return [
      'simplest case' => [ 1.0, [1], [] ],
      'simple case' => [ 2.0, [1,2,3], [] ],
      'remove case' => [ 7/3, [1,2,3,4], [3] ],
      'empty case' => [ 0.0, [1,2,3], [2,3,1]]
    ];
  }

  /*
  public function testMeanIsStillCorrect() {
      $sut = new MassMean();

      $sut->AddValue(1);
      $sut->AddValue(2);
      $sut->AddValue(3);
      $sut->AddValue(4);
      $sut->RemoveValue(3);

      self::assertSame(7/3, $sut->getMean());
  }
  */
}
