<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class AdminListUnit {
  protected $title, $content, $class, $width, $order, $obj;
  
  public function __construct($title = null) {
    $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    
    if (!($this instanceof AdminListSort))
      foreach ($traces as $trace)
        if (isset($trace['object']) && $trace['object'] instanceof AdminList && method_exists($trace['object'], 'appendUnit') && $trace['object']->appendUnit($this))
          break;

    foreach ($traces as $trace)
      if (isset($trace['function']) && $trace['function'] == '{closure}' && $trace['args'] && isset($trace['args'][0]) && $trace['args'][0] instanceof \M\Model && $this->obj($trace['args'][0]))
        break;

    $this->title($title);
  }

  public static function create($title = null) {
    return new static($title);
  }

  public function obj($obj) {
    $this->obj = $obj;
    return $this;
  }

  public function getObj() {
    return $this->obj;
  }

  public function title($title) {
    $this->title = $title;
    return $this;
  }
  
  public function content($content) {
    $this->content = $content;
    return $this;
  }

  public function getContent() {
    return $this->content;
  }
  
  public function className($class) {
    $this->class = $class;
    return $this;
  }

  public function width($width) {
    $this->width = $width;
    return $this;
  }

  public function order($order) {
    $this->order = $order;
    return $this;
  }

  public function attrs() {
    $attrs = [];
    $this->class && $attrs['class'] = $this->class;
    $this->width && $attrs['width'] = $this->width;
    return attr($attrs);
  }

  public function __toString() {
    return $this->tdString();
  }

  public function tdString() {
    return '<td' . $this->attrs() . '>' . $this->getContent() . '</td>';
  }

  public function thString($sortUrl) {
    return '<th' . $this->attrs() . '>' . AdminListOrder::set($this->title, $sortUrl ? '' : $this->order) . '</th>';
  }
}