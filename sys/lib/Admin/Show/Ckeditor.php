<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShowCkeditor extends AdminShowUnitNomin {
  public function content($items) {
    parent::content($items);
    return $this->className('ckeditor');
  }
}