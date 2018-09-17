<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShowItems extends AdminShowUnitNomin {
  public function content($items) {
    parent::content(implode('', array_map(function($item) { return '<span>' . $item . '</span>'; }, $items)));
    return $this->className('items');
  }
}