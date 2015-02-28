<?php

require_once('../BigDataUtils/NeuralNetwork.php');


// We create a NeuralNetwork with a structure of
//  2 inputs, 
//  1 hidden layer with 5 nodes
//  1 output
$nn = new NeuralNetwork(2, 5, 1);

// The network training algorithm is online, so we'll need
//  many training sets to produce an acurate result
//  20000 should be fine for our test.
for ($i = 0; $i < 20000; ++$i) {
  // produce random inputs 0 or 1
  $input_1 = mt_rand(0, 1);
  $input_2 = mt_rand(0, 1);
  // produce the output in this case the XOR function
  $result = $input_1 ^ $input_2;
  
  // Train the neural network with the training set we created above.
  $nn->Train(array($input_1, $input_2), $result);
}

// Hopefully NeuralNetwork has finished training.
// Print the Hypothesis for each possible input, did it succeed?
//  Note that Hypothesis will not return a binary value, but a value between 0 and 1.
//   a value very close to 0  < 0.1 should be interpreted as a 0
//   a value very close to 1, > 0.9 should be interpreted as a 1
//   values inbetween should be interpreted as something has gone bad...
//    -Not enough training sets
//    -Bad model of data
//    -Bad model of network
//    -And so on...
print_r($nn->Hypothesis(array(0, 0)));
print_r($nn->Hypothesis(array(0, 1)));
print_r($nn->Hypothesis(array(1, 0)));
print_r($nn->Hypothesis(array(1, 1)));
