<?php

class NeuralNetwork {

  // Array indexed by layer number between
  // each position contains the number of
  // nodes in the given layer (excluding bias)
  private $_nodes;

  // Array indexed by layer, receiver and emitter
  // each position contains the weight of the relation
  // betwen the emitter node in layer - 1, and
  // the receiver node in layer.
  private $_weights;

  // Array indexed by layer and node
  // each position contains the activation value
  // of the node in the layer.
  private $_activations;

  function __construct($architecture) {
    // Handle initialization such as NeuralNetwork(3,5,1);
    if (!is_array($architecture)) $architecture = func_get_args();

    // We need at least 2 layers. (Input and output)
    assert(count($architecture) >= 2);

    // All values on $architecture must be >= 1
    foreach ($nodes in $architecture) assert($nodes >= 1);

    $this->_Init($architecture);  
  }

  private function _Init($architecture) {
    // PRE :: $architecture is an array
    //     :: $architecture has length >= 2
    //     :: for each $i, $architecture[$i] >= 1

    $this->_nodes = $architecture;

    $layers = count($this->_nodes);

    // Initialize the weights for each layer
    // Layers are indexed from 1 to L
    // Layer number 0 is the input layer, that doesn't receive any input.
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

    // Initialize the activations for each layer
    // Layers are indexed from 0 to L
    $this->_activations = array();
    for ($layer = 0; $layer < $layers; ++$layer) {
      // Initialize the activations for each node
      // Nodes are indexed from 1 to N
      // Node number 0 is the bias, and it's value will always be 1,
      // so it's not kept for convinience.
      $this->_activations[$layer] = array_fill(1, $this->_nodes[$layer], 0);
    }
  }

  public function Train($inputs, $outputs) {
    // Handle cases with a single input
    if (!is_aray($inputs)) $input = array($inputs);
    // Handle cases with a single output
    if (!is_array($outputs)) $output = array($outputs);

    // Size of input array must be equal to the number of input units
    assert(count($inputs) == $this->_nodes(0));
    // Size of output array must be equal to the number of output units
    assert(count($outputs) == $this->_nodes(count($this->_nodes)-1));
  }

  private function _ForwardPropagate(&$weights, &$inputs) {
    // Initialize the activations of the first layer with the input values
    for ($node = 1; $node < $this->_nodes[0]; ++$node) {
      $this->_activations[0][$node] = $inputs[$node-1];
    }
    
    // Forward-propagate the activation
    // For each layer which receives inputs
    for ($layer = 1; $layer < $count($this->_nodes); ++$layer) {
      // For each node within the layer, that receives inputs
      for ($r_node = 1; $r_node < $this->_nodes[$layer] + 1; ++$r_node) {
        // Start the calculation with the bias unit from the previous layer.
        $res = $this->_weights[$layer][$r_node][0];
        // For each node in the prvious layer, that an emitter
        // (Excluding bias cause we already used it)
        for ($e_node = 1; $e_node < $this->_nodes[$layer - 1] + 1; ++$e_node) {
          // Acumulate the activations of the emitter nodes
          // multiplied by the weight of the relation
          $res += $this->_weights[$layer][$r_node][$e_node] * 
                  $this->_activations[$layer-1][$e_node];
        }
        // The activation for the node is computed by applying sigmoid
        $this->_activations[$layer][$r_node] = $this->_Sigmoid($res);
      }
    } 
  }

  public function Hypothesis($inputs) {
    return $this->_Hypothesis($this->_weights, $inputs);
  }

  private function _Hypothesis(&$weights, &$inputs) {
    $this->_ForwardPropagate($weights, $inputs);
    return $this->_activations[count($this->_nodes) - 1];
  }

  private function _Cost(&$weights, &$inputs, &$outputs) {
    $res = 0;
    
  }

  private function _Sigmoid($value) {
    return 1/(1 + pow(M_E, -$value));
  }




























}
