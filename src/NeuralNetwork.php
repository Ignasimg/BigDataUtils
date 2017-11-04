<?php

namespace IgnasiMG\BigDataUtils;

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

  // Array indexed by layer and node
  // each position contains the error of the activation
  // of the node in the layer.
  private $_errors; 

  function __construct($architecture) {
    // Handle initialization such as NeuralNetwork(3,5,1);
    if (!is_array($architecture)) $architecture = func_get_args();

    // We need at least 2 layers. (Input and output)
    assert(count($architecture) >= 2);

    // All values on $architecture must be >= 1
    for ($layer = 0; $layer < count($architecture); ++$layer) 
      assert($architecture[$layer] >= 1);

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

    // Initialize the error for each layer
    // Layers are indexed from 1 to L
    // Layer 0 (input layer) is not present since activation values
    // for its nodes are always correct
    $this->_errors = array();
    for ($layer = 1; $layer < $layers; ++$layer) {
      // Initialize the errors for each node
      // Nodes are indexed from 1 to N
      // Node number 0 is the bias, and it's activation is also always correct.
      $this->_errors[$layer] = array_fill(1, $this->_nodes[$layer], 0);
    }
  }

  public function Train($inputs, $outputs) {
    // PRE :: inputs value/s are between 0 and 1
    //     :: outputs value/s are exactly 0 or 1

    // Handle cases with a single input
    if (!is_array($inputs)) $inputs = array($inputs);
    // Handle cases with a single output
    if (!is_array($outputs)) $outputs = array($outputs);

    // Size of input array must be equal to the number of input units
    assert(count($inputs) == $this->_nodes[0]);
    // Size of output array must be equal to the number of output units
    assert(count($outputs) == $this->_nodes[count($this->_nodes) - 1]);

    // We calculate the actual cost (error), before we start any training
    $initial_cost = $this->_Cost($this->_weights, $inputs, $outputs);
    
    // If initial_cost it's already low we avoid wasting time trying to train
    if ($initial_cost <= 0.00001) return ;
    
    // Propagate the inputs
    // We don't need to call _ForwardPropagate, since we already did it
    // inside _Cost function
    // $this->_ForwardPropagate($this->_weights, $inputs);

    // Propagate the errors backwards
    $this->_BackwardPropagate($outputs);

    // Calculate the partial derivatives of _Cost with respect to _weights
    // For each layer that has receiver nodes.
    $partialDerivativesCost = array();
    for ($layer = 1; $layer < count($this->_nodes); ++$layer) {
      // For each receiver node within layer.
      $partialDerivativesCost[$layer] = array();
      for ($r_node = 1; $r_node < $this->_nodes[$layer] + 1; ++$r_node) {
        // For each emitter node in previous layer.
        // Excluding bias because we add it manually.
        $partialDerivativesCost[$layer][$r_node] = array($this->_errors[$layer][$r_node]);
        for ($e_node = 1; $e_node < $this->_nodes[$layer - 1] + 1; ++$e_node) {
          // Compute the partial derivative
          $partialDerivativesCost[$layer][$r_node][$e_node] = 
            $this->_activations[$layer - 1][$e_node] * $this->_errors[$layer][$r_node];
        }
      }
    }

    // Based on the partial derivative of cost, try to guess better weights
    // TODO :: this part could be greatly improved
    $new_weights = $this->_weights;
    $learning_rate = 1;
    // For each layer that holds receiver nodes.
    for ($layer = 1; $layer < count($this->_nodes); ++$layer) {
      // For each receiver node within the layer
      for ($r_node = 1; $r_node < $this->_nodes[$layer] + 1; ++$r_node) {
        // For each emitter node in the previous layer
        for ($e_node = 0; $e_node < $this->_nodes[$layer - 1] + 1; ++$e_node) {
          $new_weights[$layer][$r_node][$e_node] -= 
            $learning_rate * $partialDerivativesCost[$layer][$r_node][$e_node];
        }
      }
    }

    // Compute cost with the new weights
    $new_cost = $this->_Cost($new_weights, $inputs, $outputs);

    // If new cost is better (lower) than initial cost, replace the weights
    if ($new_cost < $initial_cost) $this->_weights = $new_weights;

  }

  private function _ForwardPropagate(&$weights, &$inputs) {
    // Initialize the activations of the first layer with the input values
    for ($node = 1; $node < $this->_nodes[0] + 1; ++$node) {
      $this->_activations[0][$node] = $inputs[$node-1];
    }
    
    // Forward-propagate the activation
    // For each layer which receives inputs
    for ($layer = 1; $layer < count($this->_nodes); ++$layer) {
      // For each node within the layer, that receives inputs
      for ($r_node = 1; $r_node < $this->_nodes[$layer] + 1; ++$r_node) {
        // Start the calculation with the bias unit from the previous layer.
        $res = $weights[$layer][$r_node][0];
        // For each node in the previous layer, that's an emitter
        // (Excluding bias cause we already used it) 
        for ($e_node = 1; $e_node < $this->_nodes[$layer - 1] + 1; ++$e_node) {
          // Accumulate the activations of the emitter nodes
          // multiplied by the weight of the relation
          $res += $weights[$layer][$r_node][$e_node] * 
                  $this->_activations[$layer-1][$e_node];
        }
        // The activation for the node is computed by applying sigmoid
        $this->_activations[$layer][$r_node] = $this->_Sigmoid($res);
      }
    } 
  }

  private function _BackwardPropagate(&$outputs) {
    $layers = count($this->_nodes);
    // Number of classes (output units)
    $classes = $this->_nodes[$layers - 1];

    // For each node in the output layer (excluding bias)
    for ($node = 1; $node < $classes + 1; ++$node) {
      // Calculate the error comparing the calculated value with the real one.
      $this->_errors[$layers - 1][$node] = $this->_activations[$layers - 1][$node] - 
                                           $outputs[$node - 1];
    }

    // Backward-propagate the error
    // For each layer which receives inputs, excluding output layer
    // since we just calculated its error on the previous block
    for ($layer = $layers - 2; $layer > 0; --$layer) {
      // For each node within the layer, that's an emitter
      // (excluding bias since it's activation it's always correct)
      for ($e_node = 1; $e_node < $this->_nodes[$layer] + 1; ++$e_node) {
        $res = 0;
        // For each node in the next layer, that's a receiver.
        // Note that the next layer, is in fact the previous one, since we travel backwards.
        for ($r_node = 1; $r_node < $this->_nodes[$layer + 1] + 1; ++$r_node) {
          // Accumulate the errors of the receiver nodes
          // multiplied by the weight of the relation
          $res += $this->_weights[$layer+1][$r_node][$e_node] *
                 $this->_errors[$layer+1][$r_node];
        }
        // We calculate emitter node error, discarding the error produced by receiver nodes.
        $this->_errors[$layer][$e_node] = 
          $res * $this->_SigmoidDerivative($this->_activations[$layer][$e_node]);
      }
    }
  }

  public function Hypothesis($inputs) {
    if (!is_array($inputs)) $inputs = func_get_args();

    assert(count($inputs) == $this->_nodes[0]);

    return $this->_Hypothesis($this->_weights, $inputs);
  }

  private function _Hypothesis(&$weights, &$inputs) {
    // PRE :: inputs is an array with length equal to the number of input units
    //          on the neural network.

    $this->_ForwardPropagate($weights, $inputs);
    return $this->_activations[count($this->_nodes) - 1];
  }

  private function _Cost(&$weights, &$inputs, &$outputs) {
    // TODO highly optimizable
    $res = 0;

    $hypothesis = $this->_Hypothesis($weights, $inputs);

    // For each output node
    for ($node = 0; $node < $this->_nodes[count($this->_nodes) - 1]; ++$node) {
      $real_output = $outputs[$node];
      $calc_output = $hypothesis[$node + 1];
      // Accomulate the error between the real output and the hypothesized one. 
      $res += -$real_output*log($calc_output) - (1-$real_output)*log(1 - $calc_output);
    }

    return $res;
  }

  private function _Sigmoid($value) {
    return 1/(1 + pow(M_E, -$value));
  }

  private function _SigmoidDerivative($value) {
    return $value * (1 - $value);
  }

}
