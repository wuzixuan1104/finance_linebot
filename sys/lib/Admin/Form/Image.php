<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminFormImage extends AdminFormUnit {
  private $val = '', $accept;
  
  public function __construct($title, $name) {
    parent::__construct($title, $name);
    $this->obj->hasImage();
  }

  public function val($val) {
    $this->val = $val;
    return $this;
  }

  public function accept($accept) {
    $this->accept = $accept;
    return $this;
  }

  protected function getContent() {
    $this->val instanceof \M\ImageUploader && $this->val = $this->val->url();

    $attrs = [
      'type' => 'file',
      'name' => $this->name,
    ];
    $this->accept && $attrs['accept'] = $this->accept;

    $return = '';
    $return .= '<div class="drop-img">';
      $return .= '<img src="' . $this->val . '" />';
      $return .= '<input' . attr($attrs) .'/>';
    $return .= '</div>';

    return $return;
  }
}