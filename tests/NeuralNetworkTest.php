<?php

namespace IgnasiMG\Tests\BigDataUtils;

use IgnasiMG\BigDataUtils\NeuralNetwork;
use PHPUnit\Framework\TestCase;

class NeuralNetworkTest extends TestCase {


  public function testNeuralNetworkIsWorking() {
    $sut = new NeuralNetwork([2, 5, 1]);

    for ($i = 0; $i < 2000; ++$i) {
      // produce random inputs 0 or 1
      $input_1 = random_int(0, 1);
      $input_2 = random_int(0, 1);
      // produce the output in this case the XOR function
      $result = $input_1 ^ $input_2;

      // Train the neural network with the training set we created above.
      $sut->Train([$input_1, $input_2], [$result]);
    }

    self::assertLessThanOrEqual(0.2, $sut->Hypothesis([0,0])[1]);
    self::assertGreaterThanOrEqual(0.8, $sut->Hypothesis([0,1])[1]);
    self::assertGreaterThanOrEqual(0.8, $sut->Hypothesis([1,0])[1]);
    self::assertLessThanOrEqual(0.2, $sut->Hypothesis([1,1])[1]);

  }
}