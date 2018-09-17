<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminFormSwitcher extends AdminFormUnit {
  private $val, $on, $off;

  public function val($val) {
    $this->val = $val;
    return $this;
  }

  public function on($on) {
    $this->on = $on;
    return $this;
  }

  public function off($off) {
    $this->off = $off;
    return $this;
  }

  protected function getContent() {
    $this->on !== null  || gg('請設定 Switcher 啟用值(on)！');
    $this->off !== null || gg('請設定 Switcher 關閉值(off)！');
    $this->val === $this->on || $this->val === $this->off || gg('Switcher 預設值請設定為 啟用值(on) 或 關閉值(off) 其中一項！');

    $value = AdminForm::$flash[$this->name] !== null && (AdminForm::$flash[$this->name] === $this->on || AdminForm::$flash[$this->name] === $this->off) ? AdminForm::$flash[$this->name] : $this->val;

    $return = '';
    $return .= '<div class="switches">';
      $return .= '<label>';
        $return .= '<input type="checkbox" name="' . $this->name . '" value="' . $this->on . '" data-off="' . $this->off . '"' . ($value !== null && $value == $this->on ? ' checked' : '') . '/>';
        $return .= '<span></span>';
      $return .= '</label>';
    $return .= '</div>';

    return $return;
  }
}