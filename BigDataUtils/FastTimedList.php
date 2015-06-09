<?php

class FastTimedList {

  private $_TTL = 0;

  private $_now = 0;

  private $_callback = NULL;

  private $_inici = 0;
  private $_fi = 0;

  private $_list;

  function __construct($maxsize, $TTL, $callback) {
    $this->_TTL = $TTL;
    $this->_callback = $callback;
    $this->_list = new SplFixedArray($maxsize);
  }

  public function Now($now) {
    assert($this->_now <= $now);

    $this->_now = $now;

    while (($this->_list[$this->_inici] != null) and ($this->_list[$this->_inici][0] + $this->_TTL <= $this->_now)) {
      if (!empty($this->_callback)) call_user_func($this->_callback, $this->_list[$this->_inici][1]);
      $this->_list[$this->_inici] = null;
      $this->_inici = ($this->_inici + 1)%$this->_list->getSize();
    }

  }

  public function Add($object) {
    // This could be solved by some dirty trick, but just feeling too lazy.
    assert($this->_list[$this->_fi] == null);

    $this->_list[$this->_fi++] = array($this->_now, $object);

    $this->_fi = $this->_fi % $this->_list->getSize();
  }
}
