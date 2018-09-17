<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class AdminShowUnitNomin extends AdminShowUnit {
  public function __construct($title = null) {
    parent::__construct($title);

    $this->isMin = false;
  }
}