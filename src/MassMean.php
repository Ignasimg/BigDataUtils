<?php

namespace IgnasiMG\BigDataUtils;

/**
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

  /**
   * Adds a value to the class.
   * Method compute the new mean and variance based on the addition of the value.
   *
   * @param float $value
   */
  public function AddValue(float $value) {
    $this->_numValues++;

    $this->_variance = ($this->_numValues == 1) ? 0 : $this->_variance + ($this->_numValues - 1)*pow($value - $this->_mean, 2)/$this->_numValues;
    $this->_mean = ($this->_numValues == 1) ? $value : $this->_mean + ($value - $this->_mean)/$this->_numValues;
  }

  /**
   * Removes a value from the class.
   * Method computes the new mean and variance based on the removal of the value.
   *
   * @param float $value
   */
  public function RemoveValue(float $value) {
    assert($this->_numValues > 0);

    $this->_numValues--;

    $this->_mean = ($this->_numValues == 0) ? 0 : $this->_mean + ($this->_mean - $value)/$this->_numValues;
    $this->_variance = ($this->_numValues == 1) ? 0 : $this->_variance - $this->_numValues*pow($this->_mean - $value, 2)/($this->_numValues + 1);
  }

  /**
   * @return float
   */
  public function getMean(): float {
    return $this->_mean;
  }

  /**
   * @return float
   */
  public function getVariance(): float {
    return $this->_variance;
  }

  /**
   * @return float
   */
  public function getStdDev(): float {
    return ($this->_numValues == 0) ? 0 : sqrt($this->_variance / $this->_numValues);
  }

  /**
   * @return int
   */
  public function getNumValues(): int {
    return $this->_numValues;
  }
}
