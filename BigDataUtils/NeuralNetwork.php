<?php

class NeuralNetwork {
  private $_nodes;

  private $_weights;

  function __construct($architecture) {
    // Handle initialization such as NeuralNetwork(3,5,1);
    if (!is_array($architecture)) $architecture = func_get_args();

    // We need at least 2 layers. (Input and output)
    assert(count($architecture) >= 2);

    // All values on $architecture must be >= 1
    foreach ($nodes in $architecture) assert($nodes >= 1);

    $this->_init($architecture);  
  }

  private function _init($architecture) {
    // PRE :: $architecture is an array
    //     :: $architecture has length >= 2
    //     :: for each $i, $architecture[$i] >= 1

    $this->_nodes = $architecture;

    $layers = count($this->_nodes);

    // Initialize the weights for each layer
    // Layers are indexed from 1 to L
    // 1 being the input layer
    // L being the output layer
    $this->_weights = array();
    for ($layer = 1; $layer < $layers; ++$layer) {
      // Initialize the weights for each node receiving a connection
      // Receiver nodes are indexed from 1 to R
      // Receiver number 0 is the bias unit, that doesn't receive any input.
      $this->_weights[$layer] = array();
      for ($receiver = 1; $receiver < $this->_nodes[$layer] + 1; ++$receiver) {
        // Initialize the weights for each node emitting a connection
        // Emitter nodes are indexed from 0 to E
        // Emitter number 0 is the bias unit, that does emit a signal.
        $this->_weights[$layer][$receiver] = array();
        for ($emitter = 0; $emitter < $this->_nodes[$layer - 1] + 1; ++$emitter) {
          // For the given connection initialize it's weights with
          // a random value between 0 and 1
          $this->_weights[$layer][$receiver][$emitter] = lcg_value();
        }
      }
    }
  }


}
