<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class AdminFormUnit {
  protected $title, $tip, $name, $need, $obj;

  public function __construct($title, $name) {
    $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    
    foreach ($traces as $trace)
      if (isset($trace['object']) && $trace['object'] instanceof AdminForm && method_exists($trace['object'], 'appendUnit') && $this->obj = $trace['object'])
        break;

    $this->obj->appendUnit($this);
    $this->title($title);
    $this->name($name);
  }

  public static function create($title, $name) {
    return new static($title, $name);
  }
  
  public function title($title) {
    $this->title = $title;
    return $this;
  }
  
  public function tip($tip) {
    $this->tip = $tip;
    return $this;
  }
  
  public function name($name) {
    $this->name = $name;
    return $this;
  }
  
  public function need($need = true) {
    $this->need = $need;
    return $this;
  }

  protected function getContent() {
    return '';
  }

  public function __toString() {
    $attrs = [];
    $this->need && $attrs['class'] = 'need';
    $this->tip && $attrs['data-tip'] = $this->tip;

    if ($this instanceof AdminFormInput && $this->type() == 'hidden')
      return $this->getContent();

    $return = '';
    $return .= '<div class="row' . ($this instanceof AdminFormSwitcher ? ' min' : '') . '">';
      $return .= '<b' . attr($attrs) . '>' . $this->title . '</b>';
      $return .= $this->getContent();
    $return .= '</div>';

    return $return;
  }
}