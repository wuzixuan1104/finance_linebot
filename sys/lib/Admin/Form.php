<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminForm {
  private $back;
  private $action;
  private $str;

  private $obj;
  private $units = [];
  private $hasImage = false;
  public static $flash;
  
  public static function create(\M\Model $obj = null) {
    return new static($obj);
  }

  public function __construct(\M\Model $obj = null) {
    $this->obj = $obj;

    $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    foreach ($traces as $trace)
      if (isset($trace['object']) && $trace['object'] instanceof AdminController && isset($trace['object']->flash['params']) && $this->setFlash($trace['object']->flash['params']))
        break;
  }
  
  public function back() {
    return $this->back ? '<div class="back">' . $this->back . '</div>' : '';
  }
  
  public function setBackUrl($back, $text = '回列表') {
    $this->back = Hyperlink::create($back)->text($text)->className('icon-36');
    return $this;
  }
  
  public function setFlash($flash) {
    AdminForm::$flash = $flash;
    return $this;
  }
  
  public function hasImage($hasImage = true) {
    $this->hasImage = $hasImage;
    return $this;
  }
  
  public function setActionUrl($action) {
    $this->action = $action;
    return $this;
  }

  private function closure($obj) {
    $closure = $this->closure;
    $closure($obj);
  }

  public function form($closure) {
    if ($this->str)
      return $this->str;

    $this->action || gg('請設定 Action 網址！');

    $this->units = [];
    
    $title = null;
    $closure($this->obj, $title);
    $title == null && $title = '';

    $this->str = '';
    
    if (!$this->units)
      return $this->str;

    $this->str .= $title ? '<span class="title">' . $title . '</span>' : '';
    $this->str .= '<form class="form" action="' . $this->action . '" method="post"' . ($this->hasImage ? ' enctype="multipart/form-data"' : '') . '>';
      $this->str .= $this->obj ? '<input type="hidden" name="_method" value="put" />' : '';
      $this->str .= implode('', $this->units);
      $this->str .= '<div class="ctrl">';
        $this->str .= '<button type="submit">確定</button>';
        $this->str .= '<button type="reset">取消</button>';
      $this->str .= '</div>';
    $this->str .= '</form>';
  
    $this->units = [];
    return $this->str;
  }

  public function appendUnit(AdminFormUnit $unit) {
    array_push($this->units, $unit);
    return $this;
  }
}
