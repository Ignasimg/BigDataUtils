<?php

namespace IgnasiMG\BigDataUtils;

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
