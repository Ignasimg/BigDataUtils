<?php

require_once('../BigDataUtils/MassMean.php');

function mean($array) {
  if (count($array) == 0) return 0;
  $res = 0;
  for ($i = 0; $i < count($array); ++$i) $res += $array[$i];
  return $res/count($array);
}

function stdDev($array) {
  if (count($array) == 0) return 0;
  $mean = mean($array);
  $res = 0;
  for ($i = 0; $i < count($array); ++$i) $res += pow($array[$i] - $mean, 2);
  return sqrt($res/count($array));
}

function checkCorrectness($massMean, $values) {
  // Since it's a numeric aproximation we will never get the exact real result.
  // We select just a few decimal places, some errors might still fire up but we can check the values are really similar.
  // We use StdDev for the check since for it's calc we also need the mean, hence if mean is wrong, stdDev will be too.
  $realStdDev = round(stdDev($values), 5);
  $calcStdDev = round($massMean->stdDev, 5);

  if ($realStdDev != $calcStdDev) {
    echo 'Error!'.PHP_EOL;
    echo 'Real value: '.$realStdDev.PHP_EOL;
    echo 'Calc value: '.$calcStdDev.PHP_EOL;
    echo PHP_EOL;
  }
}

$massMean = new MassMean();

$values = array();

for ($i = 0; $i < 1000; ++$i) {
  $new_value = rand(0, 1000000) - 500000;


  $values[] = $new_value;
  $massMean->AddValue($new_value);

  checkCorrectness($massMean, $values);
}

do {
  $index = rand(1, count($values)) - 1;
  $massMean->RemoveValue($values[$index]);
  array_splice($values, $index, 1);
  checkCorrectness($massMean, $values);
} while (count($values) > 0);
