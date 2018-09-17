<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShowImage extends AdminShowMedias {
  public function content($src) {
    return parent::content([$src]);
  }
}