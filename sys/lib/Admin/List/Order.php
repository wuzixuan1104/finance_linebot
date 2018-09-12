<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListOrder {
  const KEY = '_o';
  const SPLIT_KEY = ':';

  private $sort = 'id DESC';

  public function __construct($sort = '') {
    if ($sort && count($sort = array_values(array_filter(array_map('trim', explode(' ', $sort))))) == 2 && in_array(strtolower($sort[1]), ['desc', 'asc']))
      $this->sort = $sort[0] . ' ' . strtoupper($sort[1]);

    if (($sort = Input::get(AdminListOrder::KEY)) && count($sort = array_values(array_filter(array_map('trim', explode(AdminListOrder::SPLIT_KEY, $sort))))) == 2 && in_array(strtolower($sort[1]), ['desc', 'asc']))
      $this->sort = $sort[0] . ' ' . strtoupper($sort[1]);
  }
  
  public static function set($title, $column = '') {
    if (!$column) return $title;

    $gets = Input::get();
    
    if (!(isset($gets[AdminListOrder::KEY]) && count($sort = array_values(array_filter(explode(AdminListOrder::SPLIT_KEY, $gets[AdminListOrder::KEY])))) == 2 && in_array(strtolower($sort[1]), ['desc', 'asc']) && ($sort[0] == $column))) {
      $gets[AdminListOrder::KEY] = $column . AdminListOrder::SPLIT_KEY . 'desc';
      return $title . ' <a href="' . Url::current() . '?' . http_build_query($gets) . '" class="order"></a>';
    }

    $class = strtolower($sort[1]);

    if ($class != 'asc')
      $gets[AdminListOrder::KEY] = $column . AdminListOrder::SPLIT_KEY . 'asc';
    else
      unset($gets[AdminListOrder::KEY]);

    return $title . ' <a href="' . Url::current() . ($gets ? '?' : '') . http_build_query($gets) . '" class="order ' . $class . '"></a>';
  }

  private static function _desc($column = '') {
    return ($column ? $column : 'id') . ' ' . strtoupper('desc');
  }

  private static function _asc($column = '') {
    return ($column ? $column : 'id') . ' ' . strtoupper('asc');
  }

  public function __call($name, $arguments) {
    switch (strtolower(trim($name))) {
      case 'asc':
        $this->sort = call_user_func_array(['self', '_asc'], $arguments);
        break;

      case 'desc':
        $this->sort = call_user_func_array(['self', '_desc'], $arguments);
        break;

      default:
        gg('AdminListOrder 沒有「' . $name . '」方法。');
        break;
    }
    return $this;
  }

  public static function __callStatic($name, $arguments) {
    switch (strtolower(trim($name))) {
      case 'asc':
        return AdminListOrder::create(call_user_func_array(['self', '_asc'], $arguments));
        break;

      case 'desc':
        return AdminListOrder::create(call_user_func_array(['self', '_desc'], $arguments));
        break;

      default:
        gg('AdminListOrder 沒有「' . $name . '」方法。');
        break;
    }
  }

  public function __toString() {
    return $this->sort;
  }

  public static function create($sort = '') {
    return new AdminListOrder($sort);
  }
}