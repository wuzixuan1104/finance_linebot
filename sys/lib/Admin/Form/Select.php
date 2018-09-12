<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminFormSelect extends AdminFormUnitItems {
  private $val = '', $focus;

  public function val($val) {
    $this->val = $val;
    return $this;
  }

  public function focus($focus = true) {
    $this->focus = $focus;
    return $this;
  }

  protected function getContent() {
    $value = AdminForm::$flash[$this->name] !== null ? AdminForm::$flash[$this->name] : $this->val;

    $attrs = [
      'name' => $this->name,
    ];
    $this->focus && $attrs['autofocus'] = $this->focus;
    $this->need && $attrs['required'] = true;

    $return = '';
    $return .= '<select' . attr($attrs) .'>';
      $return .= '<option value=""' . ($value == '' ? ' selected' : '') . '>請選擇' . $this->title . '</option>';
      $return .= implode('', array_map(function($item) use ($value) {
        return '<option value="' . $item['value'] . '"' . ($value == $item['value']  ? ' selected' : '') . '>' . $item['text'] . '</option>';
      }, $this->items));
    $return .= '</select>';

    return $return;
  }
}