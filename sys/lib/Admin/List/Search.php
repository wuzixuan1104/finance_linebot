<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class AdminListSearch {
  protected $title, $sql, $val, $key;

  public function __construct($title = null, $sql = null) {
    $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    
    if (!($this instanceof AdminListSort))
      foreach ($traces as $trace)
        if (isset($trace['object']) && $trace['object'] instanceof AdminList && method_exists($trace['object'], 'appendSearch') && $trace['object']->appendSearch($this))
          break;

    $this->title($title);
  }

  public function title($title = null) {
    if ($title === null)
      return $this->title;

    $this->title = $title;
    return $this;
  }

  public function sql($sql = null) {
    if ($sql === null)
      return $this->sql;

    $this->sql = $sql;
    return $this;
  }

  public function key($key = null) {
    if ($key === null)
      return $this->key;

    $this->key = $key;
    return $this;
  }

  public function updateSql($val) {
    if ($val === null || $val === '' || (is_array($val) && !count($val)) || empty($this->sql))
      return null;

    $sql = $this->sql;
    $this->val = $val;

    if (is_callable($sql))
      $sql = $sql($this->val);

    if (is_string($sql))
      return Where::create($sql, strpos(strtolower($sql), ' like ') !== false ? '%' . $this->val . '%' : $this->val);

    if (is_object($sql) && $sql instanceof Where)
      return $sql;

    return null;
  }

  public static function create($title = null, $sql = null) {
    return new static($title, $sql);
  }
  
  abstract public function __toString();
}