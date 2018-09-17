<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Hyperlink {
  private $href, $text, $class, $target, $style, $attrs = [];
  public function href($href)       { $this->href   = $href instanceof \M\Uploader ? $href->url() : $href;   return $this; }
  public function text($text)       { $this->text   = $text;   return $this; }
  public function style($style)     { $this->style  = $style;  return $this; }
  public function attrs($attrs)     { $this->attrs  = $attrs;  return $this; }
  public function target($target)   { $this->target = $target; return $this; }
  public function className($class) { $this->class  = $class;  return $this; }

  public function __construct($href = null) {
    $this->href($href);
  }

  public static function create($href = null) {
    return new static($href);
  }

  public function __toString() {
    $attrs = [];
    $this->href   && $attrs['href']   = $this->href;
    $this->class  && $attrs['class']  = $this->class;
    $this->style  && $attrs['style']  = $this->style;
    $this->target && $attrs['target'] = $this->target;
    $this->attrs  && $attrs = array_merge($this->attrs, $attrs);

    return '<a' . attr($attrs) . '>' . $this->text . '</a>';
  }
}

class Span {
  private $text, $class, $style, $attrs = [];

  public function __construct($text = null) {
    $this->text($text);
    
  }

  public static function create($text = null) {
    return new static($text);
  }

  public function text($text)       { $this->text   = $text;  return $this; }
  public function style($style)     { $this->style  = $style; return $this; }
  public function className($class) { $this->class  = $class; return $this; }
  public function attrs($attrs)     { $this->attrs  = $attrs; return $this; }

  public function __toString() {
    $attrs = [];
    $this->class  && $attrs['class']  = $this->class;
    $this->style  && $attrs['style']  = $this->style;
    $this->attrs  && $attrs = array_merge($this->attrs, $attrs);

    return '<span' . attr($attrs) . '>' . $this->text . '</span>';
  }
}