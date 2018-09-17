<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShowImages extends AdminShowMedias {
  public function content($srcs) {
    return parent::content($srcs);
  }
}