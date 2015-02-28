<?php

require_once('../BigDataUtils/NeuralNetwork.php');

$nn = new NeuralNetwork(2, 3, 1);

for ($i = 0; $i < 20000; ++$i) {
  $input_1 = rand(0, 1);
  $input_2 = rand(0, 1);
  $result = $input_1 ^ $input_2;

  $nn->Train(array($input_1, $input_2), $result);
}

print_r($nn->Hypothesis(array(0, 0)));
print_r($nn->Hypothesis(array(0, 1)));
print_r($nn->Hypothesis(array(1, 0)));
print_r($nn->Hypothesis(array(1, 1)));
