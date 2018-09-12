<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListCheckbox extends AdminListImages {
  private $attrs = [];

  public function attr($key, $val) {
    $this->attrs[$key] = $val;
    return $this;
  }

  public function getContent() {
    $return = '';
    $return .= '<label ' . attr(['class' => 'checkbox']) . '>';
      $return .= '<input type="checkbox"' . attr($this->attrs) . '/>';
      $return .= '<span></span>';
    $return .= '</label>';

    return $return;
  }
}