<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListItems extends AdminListUnit {
  public function content($content) {
    parent::content(implode('', array_map(function($t) { return '<span>' . $t . '</span>'; }, $content)));
    return $this->className('items');
  }
}