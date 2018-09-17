<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminFormTextarea extends AdminFormUnit {
  private $val = '', $type = 'pure', $placeholder, $focus, $minLength, $maxLength;

  public function val($val) {
    is_string($val) && $this->val = $val;
    return $this;
  }

  public function type($type) {
    $this->type = $type;
    return $this;
  }

  public function placeholder($placeholder) {
    $this->placeholder = $placeholder;
    return $this;
  }

  public function focus($focus = true) {
    $this->focus = $focus;
    return $this;
  }

  public function minLength($minLength) {
    $this->minLength = $minLength;
    return $this;
  }
  
  public function maxLength($maxLength) {
    $this->maxLength = $maxLength;
    return $this;
  }

  protected function getContent() {
    $value = AdminForm::$flash[$this->name] !== null ? AdminForm::$flash[$this->name] : $this->val;
    $this->need && ($this->minLength === null || $this->minLength <= 0) && $this->minLength(1);

    $attrs = [
      'class' => $this->type,
      'name' => $this->name,
    ];

    $this->need && $attrs['required'] = true;
    $this->focus && $attrs['autofocus'] = true;
    $this->minLength && $attrs['minlength'] = $this->minLength;
    $this->maxLength && $attrs['maxlength'] = $this->maxLength;
    $this->placeholder && $attrs['placeholder'] = $this->placeholder;

    return '<textarea' . attr($attrs) .'>' . $value . '</textarea>';
  }
}