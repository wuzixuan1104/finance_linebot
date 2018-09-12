<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListSearchCheckbox extends AdminListSearchItems {
  public function __toString() {
    $return = '';
    
    if (!$this->items)
      return $return;

    $return .= '<div class="row">';
    $return .= '<b>' . $this->title . '</b>';
    $return .= '<div class="checkboxs">';
    $return .= implode('', array_map(function($item) {
      return '<label><input type="checkbox" name="' . $this->key . '[]" value="' . $item['value'] . '"' . ($this->val && (is_array($this->val) ? in_array($item['value'], $this->val) : $this->val == $item['value']) ? ' checked' : '') . ' /><span></span>' . $item['text'] . '</label>';
    }, $this->items));
    $return .= '</div>';
    $return .= '</div>';

    return $return;
  }
}