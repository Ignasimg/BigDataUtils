<?php

class TimedList {

  private $_TTL = 0;

  private $_now = 0;

  private $_callback = NULL;

  private $_list;

  function __construct($TTL, $callback) {
    $this->_TTL = $TTL;
    $this->_callback = $callback;
    $this->_list = array();
  }

  public function Now($now) {
    assert($this->_now <= $now);

    $this->_now = $now;

    while (!empty($this->_list) and ($this->_list[0][0] + $this->_TTL <= $this->_now)) {
      if (!empty($this->_callback)) call_user_func($this->_callback, $this->_list[0][1]);
      array_splice($this->_list, 0, 1);
    }
  }

  public function Add($object) {
    $this->_list[] = array($this->_now, $object);
  }
}
