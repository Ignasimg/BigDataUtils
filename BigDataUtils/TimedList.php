<?php

class TimedList {

  private $_TTL = 0;

  private $_now = 0;

  private $_callback = NULL;

  private $_list;

  function __construct($TTL, $callback) {
    assert($TTL > 0);
    assert(is_callable($callback));
    $this->_TTL = $TTL;
    $this->_callback = $callback;
    $this->_list = new SplQueue();
  }

  public function Now($now) {
    assert($this->_now <= $now);

    $this->_now = $now;

    while (!$this->_list->isEmpty() and ($this->_list->bottom()[0] + $this->_TTL <= $this->_now)) {
      call_user_func($this->_callback, $this->_list->dequeue()[1]);
    }
  }

  public function Add($object) {
    $this->_list->enqueue(array($this->_now, $object));
  }
}
