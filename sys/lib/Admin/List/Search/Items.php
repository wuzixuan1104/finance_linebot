<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class AdminListSearchItems extends AdminListSearch {
  protected $items = [];

  public function items(array $items) {
    if (!$items)
      return $this;

    is_string(array_values($items)[0]) && $items = items(array_keys($items), array_values($items));

    $this->items = $items;
    return $this;
  }
}