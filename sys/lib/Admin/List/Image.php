<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListImage extends AdminListImages {
  public function content($content) {
    return parent::content([$content]);
  }
}