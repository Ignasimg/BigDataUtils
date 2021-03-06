<?php

namespace IgnasiMG\BigDataUtils;

// Explanation http://keithschwarz.com/darts-dice-coins/
// Source slightly tuned from https://gist.github.com/tystr/3171981

class Probability {
    private $_N = 0;
    private $_prob = [];
    private $_alias = [];

  /**
   * Probability constructor.
   *
   * @param array $probabilities
   */
  function __construct(array $probabilities) {
        $small = $large = [];

        $this->_N = count($probabilities);
        $probabilities = array_map(function($p) {
            return $p * $this->_N; // Scale each probability by n
        }, $probabilities);

        for ($i = 0; $i < $this->_N; ++$i) {
            if ($probabilities[$i] < 1) {
                $small[] = $i;
            }
            else {
                $large[] = $i;
            }
        }

        while (!empty($small) && !empty($large)) {
            $l = array_shift($small);
            $g = array_shift($large);
            $this->_prob[$l] = $probabilities[$l];
            $this->_alias[$l] = $g;
            $probabilities[$g] = ($probabilities[$g] + $probabilities[$l]) - 1;
            if ($probabilities[$g] < 1) {
                $small[] = $g;
            } else {
                $large[] = $g;
            }
        }

        while (!empty($large)) {
            $this->_prob[array_shift($large)] = 1;
        }
        while (!empty($small)) {
            $this->_prob[array_shift($small)] = 1;
        }
    }

  /**
   * @return int
   */
  public function draw() {
        $i = random_int(0, $this->_N-1);
        $coinToss = 1 * random_int(0, PHP_INT_MAX) / PHP_INT_MAX < $this->_prob[$i];
        return $coinToss ? $i : $this->_alias[$i];
    }
}
