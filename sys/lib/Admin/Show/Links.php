<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShowLinks extends AdminShowUnitNomin {
  public function content($items) {
    parent::content(implode('', array_map(function($item) {
      if (is_array($item))
        return Hyperlink::create(array_shift($item))->text(array_shift($item))->className('icon-45');
      else
        return Hyperlink::create($item)->text($item)->className('icon-45');
    }, $items)));

    return $this->className('links');
  }
}