<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListSearchInput extends AdminListSearch {
  private $type;
  
  public function type($type) {
    $this->type = $type;
    return $this;
  }

  public function __toString() {
    $return = '';
    $return .= '<label class="row">';
    $return .= '<b>' . $this->title . '</b>';
    $return .= '<input name="' . $this->key . '" type="' . ($this->type ? $this->type : 'text') . '" placeholder="' . $this->title . '" value="' . $this->val . '" />';
    $return .= '</label>';
    return $return;
  }
}