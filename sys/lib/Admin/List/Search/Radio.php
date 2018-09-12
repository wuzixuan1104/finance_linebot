<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListSearchRadio extends AdminListSearchItems {
  public function __toString() {
    $return = '';
    
    if (!$this->items)
      return $return;

    $return .= '<div class="row">';
    $return .= '<b>' . $this->title . '</b>';
    $return .= '<div class="radios">';
    $return .= implode('', array_map(function($item) { return '<label><input type="radio" name="' . $this->key . '" value="' . $item['value'] . '"' . ($this->val && $this->val == $item['value'] ? ' checked' : '') . ' /><span></span>' . $item['text'] . '</label>'; }, $this->items));
    $return .= '</div>';
    $return .= '</div>';
    return $return;
  }
}