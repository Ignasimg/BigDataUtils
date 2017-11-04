<?php

namespace IgnasiMG\BigDataUtils;

/*
* BigDataUtils
*
* MassMean class - utility to compute the mean, variance or std.Dev of a continuous stream of values, 
*   in an efficient way. While also being capable of removing some values from the calculation on a later date.
*/
class MassMean {

  private $_mean;

  private $_variance;

  private $_numValues;

  function __construct() {
    $this->_mean = 0;
    $this->_variance = 0;
    $this->_numValues = 0;
  }

  /*
  * Adds a value to the class.
  * Method compute the new mean and variance based on the addition of the value.
  */
  public function AddValue($value) {
    $this->_numValues++;

    $this->_variance = ($this->_numValues == 1) ? 0 : $this->_variance + ($this->_numValues - 1)*pow($value - $this->_mean, 2)/$this->_numValues;
    $this->_mean = ($this->_numValues == 1) ? $value : $this->_mean + ($value - $this->_mean)/$this->_numValues;
  }

  /*
  * Removes a value from the class.
  * Method computes the new mean and variance based on the removal of the value.
  */
  public function RemoveValue($value) {
    assert($this->_numValues > 0);

    $this->_numValues--;

    $this->_mean = ($this->_numValues == 0) ? 0 : $this->_mean + ($this->_mean - $value)/$this->_numValues;
    $this->_variance = ($this->_numValues == 1) ? 0 : $this->_variance - $this->_numValues*pow($this->_mean - $value, 2)/($this->_numValues + 1);
  }

  /*
  * Get the mean, variance and stdDev from the set of values you've been adding / removing.
  */
  public function __get($property) {
    switch ($property) {
      case 'mean' : return $this->_mean;
        break;
      case 'variance' : return $this->_variance;
        break;
      case 'stdDev' : return ($this->_numValues == 0) ? 0 : sqrt($this->_variance / $this->_numValues);
        break;
      case 'numValues' : return $this->_numValues;
        break;
      default : break;
    }
  }

}
