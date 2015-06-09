<?php
/* 
 * An instance of the ski rental problem
 * Info :: http://en.wikipedia.org/wiki/Ski_rental_problem
 */
class BuyOrRent {

  private $_rent;
  
  private $_buy;

  private $_day;

  // TODO
  private $_randomized;

  function __construct($buyPrice, $rentPrice) {
    $this->_rent = $rentPrice;
    $this->_buy = $buyPrice;
    $this->_day = 1;
  }

  public function CompetitiveRatio() {
    return $this->_CompetitiveRatio($this->_day);
  }

  private function _CompetitiveRatio($day) {
    return ($this->_buy + $this->_rent*($day - 1))/($this->_rent * $day);
  }

  public function CallItADay() {
    ++$this->_day;
  }

  public function Buy() {
    return $this->_CompetitiveRatio($this->_day) < $this->_CompetitiveRatio($this->_day + 1);
  }

  public function Rent() {
    return !$this->Buy();
  }
}
