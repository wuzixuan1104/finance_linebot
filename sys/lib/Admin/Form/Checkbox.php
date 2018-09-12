<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminFormCheckbox extends AdminFormUnitItems {
  private $val = [];

  public function val(array $val) {
    $this->val = $val;
    return $this;
  }

  public static function inArray($var, $arr) {
    foreach ($arr as $val)
      if (($var === 0 ? '0' : $var) == $val)
        return true;
    return false;
  }

  protected function getContent() {
    $value = AdminForm::$flash !== null ? isset(AdminForm::$flash[$this->name]) ? AdminForm::$flash[$this->name] : [] : $this->val;
    is_array($value) || $value = [];

    $return = '';

    $return .= '<div class="checkboxs">';
    $return .= implode('', array_map(function($item) use ($value) {
      $return = '';
      $return .= '<label>';
        $return .= '<input type="checkbox" value="' . $item['value'] . '" name="' . $this->name . '[]"' . (AdminFormCheckbox::inArray($item['value'], $value) ? ' checked' : '') . '/>';
        $return .= '<span></span>';
        $return .= $item['text'];
      $return .= '</label>';
      return $return;
    }, $this->items));
    $return .= '</div>';

    return $return;
  }
}