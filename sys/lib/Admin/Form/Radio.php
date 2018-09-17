<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminFormRadio extends AdminFormUnitItems {
  private $val;

  public function val($val) {
    $this->val = $val;
    return $this;
  }

  protected function getContent() {
    $value = AdminForm::$flash[$this->name] !== null ? AdminForm::$flash[$this->name] : $this->val;

    $return = '';

    $return .= '<div class="radios">';
    $return .= implode('', array_map(function($item) use ($value) {
      $return = '';
      $return .= '<label>';
        $return .= '<input type="radio" name="' . $this->name . '" value="' . $item['value'] . '"' . ($this->need === true ? ' required' : '') . ($value !== null && $value == $item['value']  ? ' checked' : '') . '/>';
        $return .= '<span></span>';
        $return .= $item['text'];
      $return .= '</label>';
      return $return;
    }, $this->items));
    $return .= '</div>';

    return $return;
  }
}

