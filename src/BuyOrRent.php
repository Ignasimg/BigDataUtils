<?php

namespace IgnasiMG\BigDataUtils;

// Explanation http://keithschwarz.com/darts-dice-coins/
// Source slightly tuned from https://gist.github.com/tystr/3171981
class Probability {
  private $_N = 0;
  private $_prob = [];
  private $_alias = [];

  function __construct($probabilities) {
    $small = $large = [];

    $this->_N = count($probabilities);
    $probabilities = array_map(function($p) {
        return $p * $this->_N; // Scale each probability by n
    }, $probabilities);

    for ($i = 0; $i < $this->_N; ++$i) {
      if ($probabilities[$i] < 1) {
        $small[] = $i;TimedList
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

  public function draw() {
    $i = mt_rand(0, $this->_N-1);
    $coinToss = 1 * mt_rand() / mt_getrandmax() < $this->_prob[$i];
    return $coinToss ? $i : $this->_alias[$i];
  }
}

// Explanation https://en.wikipedia.org/wiki/Ski_rental_problem
class BuyOrRent {

  static function rentingDays($rentprice, $buyprice) {
    // If rent is free keep on renting forever
    if ($rentprice == 0) return -1;

    // If $rentprice became 1€, what would proportionally be $buyprice?
    $b = $buyprice/$rentprice;

    // Generem la funció de probabilitat que està definida entre 1 i $b
    // Però els index de $pi aniràn de 0 a $b-1
    $pi = [];
    for ($i = 1; $i <= $b; ++$i) {
      $pi[] = pow(($b-1)/$b, $b-$i)/($b*(1-pow(1-(1/$b),$b)));
    }

    // $p generarà una probabilitat entre 0 i $b-1
    $p = new Probability($pi);

    // La $i real seleccionada seria el valor + 1
    // No obstant el lloguer dura $i - 1 dies per tant així és correcte
    return $p->draw();
  }
}



// Experimentation
$m = new MassMean();

for ($i = 0; $i < 3000; $i++) {
  $pc = 1000; // Preu compra
  $rp = 1;  // Rent price

  $real_days = 1 * mt_rand(1, 1500);
  $correct_amount = (($real_days*$rp) > $pc) ? $pc : ($real_days*$rp);

  $rentingDays = BuyOrRent::rentingDays($rp, $pc);
  $real_amount = ($rentingDays >= $real_days) ? ($real_days*$rp) : ($rentingDays*$rp)+$pc;

  $m->AddValue($real_amount/$correct_amount);
}

var_dump($m->mean);
var_dump($m->variance);
var_dump($m->stdDev);
var_dump($m->numValues);
