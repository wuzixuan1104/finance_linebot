<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListSearchSelect extends AdminListSearchItems {
  public function __toString() {
    $return = '';
    
    if (!$this->items)
      return $return;

    $return .= '<label class="row">';
    $return .= '<b>' . $this->title . '</b>';
    $return .= '<select name="' . $this->key . '">';
    $return .= '<option value="">' . $this->title . '</option>';
    $return .= implode('', array_map(function($item) { return '<option value="' . $item['value'] . '"' . ($this->val && $this->val == $item['value'] ? ' selected' : '') . '>' . $item['text'] . '</option>'; }, $this->items));
    $return .= '</select>';
    $return .= '</label>';

    return $return;
  }
}