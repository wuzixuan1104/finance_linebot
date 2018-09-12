<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Where {
  private $where = [];

  protected function __construct($where = []) {
    $this->where = $where;
  }

  public function __toString() {
    return $this->toString();
  }

  public function toString() {
    return call_user_func_array('sprintf', preg_replace('/\?/', '%s', $this->where ? $this->where : ['']));
  }

  public function toArray() {
    return $this->where;
  }

  public static function create() {
    if(!$args = func_get_args())
      return new Where([]);

    $where = array_shift($args);

    if(is_string($where))
      $where = call_user_func_array(array('self', 'and'), array_merge([[]], [$where], $args));

    return new Where($where);
  }

  public function __call($name, $arguments) {
    switch ($name) {
      case 'and':
        $this->where = call_user_func_array(['self', '_and'], array_merge([$this->where], $arguments));
        break;

      case 'or':
        $this->where || $this->where = [array_shift($arguments)];
        $this->where = call_user_func_array(['self', '_or'], array_merge([$this->where], $arguments));
        break;

      default:
        gg('Where 沒有「' . $name . '」方法。');
        break;
    }
    return $this;
  }

  public static function _and() {
    if(!$args = func_get_args())
      return [];

    $where = array_shift($args);
    
    if(is_string($where))
      return call_user_func_array(array('self', 'and'), array_merge([[]], [$where], $args));

    is_array($args[0]) && $args = $args[0];
    $str = array_shift($args);

    if($str instanceof Where) {
      $args = $str->toArray();
      $str = array_shift($args);
    }

    $c = substr_count ($str, '?');
    count($args) < $c && gg('參數錯誤。「' . $str . '」 有 ' . $c . ' 個參數，目前只給 ' . count($args) . ' 個。');

    $where[0] = $where ? '(' . $where[0] . ')' . ($str ? ' AND (' . $str . ')': '') : $str;

    foreach(array_splice($args, 0, $c) as $arg)
      $arg === null || array_push($where, $arg);
    
    return $where;
  }

  public static function _or() {
    if(!$args = func_get_args())
      return [];

    $where = array_shift($args);
    
    if(is_string($where))
      return call_user_func_array(array('self', 'and'), array_merge([[]], [$where], $args));

    is_array($args[0]) && $args = $args[0];
    $str = array_shift($args);
    if($str instanceof Where) {
      $args = $str->toArray();
      $str = array_shift($args);
    }

    $c = substr_count ($str, '?');
    count($args) < $c && gg('參數錯誤。「' . $str . '」 有 ' . $c . ' 個參數，目前只給 ' . count($args) . ' 個。');

    $where[0] = $where ? '(' . $where[0] . ')' . ($str ? ' OR (' . $str . ')' : '') : '(' . $str . ')';

    foreach(array_splice($args, 0, $c) as $arg)
      $arg === null || array_push($where, $arg);
    
    return $where;
  }

  public static function __callStatic($name, $arguments) {
    switch($name) {
      case 'and':
        return call_user_func_array(array('self', '_and'), $arguments);
        break;

      case 'or':
        return call_user_func_array(array('self', '_or'), $arguments);
        break;
      
      default:
        gg('Where 沒有「' . $name . '」方法。');
        break;
    }
  }
}