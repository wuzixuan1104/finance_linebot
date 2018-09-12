<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShowText extends AdminShowUnit {
  public function isMin($isMin = true) {
    $this->isMin = $isMin;
    return $this;
  }
}
