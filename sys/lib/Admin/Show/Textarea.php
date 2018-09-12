<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShowTextarea extends AdminShowUnitNomin {
  public function content($items) {
    return parent::content(nl2br($items));
  }
}
