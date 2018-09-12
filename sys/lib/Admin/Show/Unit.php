<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class AdminShowUnit {
  protected $title, $content, $class, $isMin = true;

  public function __construct($title = null) {
    $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    
    foreach ($traces as $trace)
      if (isset($trace['object']) && $trace['object'] instanceof AdminShow && method_exists($trace['object'], 'appendUnit') && $trace['object']->appendUnit($this))
        break;

    $this->title($title);
  }

  public static function create($title = null) {
    return new static($title);
  }
  
  public function title($title) {
    $this->title = $title;
    return $this;
  }
  
  public function content($content) {
    $this->content = $content;
    return $this;
  }
  
  public function className($class) {
    $this->class = $class;
    return $this;
  }

  public function __toString() {
    return '<div class="unit' . ($this->isMin ? ' min' : '') . '"><b>' . $this->title . '</b><div class="' . $this->class . '">' . $this->content . '</div></div>';
  }
}