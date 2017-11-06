<?php

namespace IgnasiMG\Tests\BigDataUtils;

use IgnasiMG\BigDataUtils\BuyOrRent;
use IgnasiMG\BigDataUtils\MassMean;
use PHPUnit\Framework\TestCase;

class BuyOrRentTest extends TestCase {

  public function testBuyOrRentWorks() {
    $m = new MassMean();

    $pc = 10; // Buy price
    $rp = 1; // Rent price

    for ($i = 0; $i < 10000; $i++) {
      // God knows how many days will our holidays last
      $days = random_int(1, 25);
      // God knows what's the cost of the best option, to rent or to buy.
      $costBasedOnKnowledge = (($days*$rp) > $pc) ? $pc : ($days*$rp);

      // Algorithm predicts a number of days to rent, based on prices.
      $predictedRentingDays = BuyOrRent::rentingDays($rp, $pc);
      // The cost of obeying the algorithm will depend whether we reach the amount of days we choose to rent.
      $costBasedOnPrediction = ($predictedRentingDays >= $days) ? ($days*$rp) : ($predictedRentingDays*$rp)+$pc;

      // We calculate the mean of cost based on algorithm vs cost based on god knowledge.
      $m->AddValue($costBasedOnPrediction/$costBasedOnKnowledge);
    }

    self::assertLessThanOrEqual(1.58, round($m->getMean(), 2));
  }
}
