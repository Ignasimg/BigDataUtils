<?php

namespace IgnasiMG\Tests\BigDataUtils;

use IgnasiMG\BigDataUtils\Probability;
use PHPUnit\Framework\TestCase;

class ProbabilityTest extends TestCase {


  /**
   * @dataProvider simpleProbabilitiesProvider
   */
  public function testDrawingIsCorrect(array $probabilities, int $box) {
    $sut = new Probability($probabilities);

    self::assertSame($box, $sut->draw());
  }

  public function simpleProbabilitiesProvider() {
    return [
      'simplest case' => [[1], 0],
      'simple case' => [[0,0,1,0], 2]
    ];
  }

  /**
   * @dataProvider probabilitiesProvider
   */
  public function testProbabilityIsCorrect(array $probabilities) {
    $sut = new Probability($probabilities);

    $box = array_fill(0, count($probabilities), 0);

    for ($i = 0; $i < 1000; ++$i) {
      $drawn = $sut->draw();
      $box[$drawn]++;
    }

    $box = array_map(function ($b) { return round($b/1000, 1); }, $box);

    for ($j = 0; $j < count($probabilities); ++$j) {
      self::assertSame($probabilities[$j], $box[$j]);
    }
  }

  public function probabilitiesProvider() {
    return [
      'half-half' => [[0.5, 0.5]],
      'progressive up' => [[0.1, 0.2, 0.3, 0.4]],
      'progressive down' => [[0.4, 0.3, 0.2, 0.1]],
      '9:1' => [[0.9, 0.1]],
      '10 equal' => [[0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1]],
      '2:6:2' => [[0.2,0.6,0.2]]
    ];
  }
}