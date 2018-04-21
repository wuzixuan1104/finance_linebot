<?php

namespace Restful;

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

defined ('OACI') || exit ('此檔案不允許讀取。');

class Show {
  private $rows = array (), $hasImage = false, $obj = array();

  public static function create () {
    return new Show ();
  }

  public function appendRows () {
    foreach (func_get_args () as $row)
      $this->appendRow ($row);
    return $this;
  }

  public function appendRow (ShowRow $row) {
    $this->hasImage |= $row instanceof \Images || $row instanceof \Image;
    array_push ($this->rows, $row);
    return $this;
  }
  public function setObj ($obj) {
    $this->obj = $obj;
    return $this;
  }

  public function __toString () {
    return $this->toString ();
  }

  public function toString () {
    $return = '';
    foreach ($this->rows as $row)
      $return .= $row->setObj ($this->obj);
    return $return;
  }
}

abstract class ShowRow {
  protected $type, $title, $func, $format;

  public function __construct() {
    $this->type = null;
    $this->func = null;
    $this->format = null;
    $this->title = null;
  }

  public function __toString () {
    return $this->toString ();
  }

  abstract public function setItem($item);

  public function setObj ($obj) { $this->obj = $obj; return $this; }
  public static function create ($title, $func) { return (new static ())->setTitle ($title)->setFunc ($func); }
  public function setFunc ($func) { is_callable ($func) && $this->func = $func; return $this; }
  public function setTitle ($title) { $title && is_string ($title) && $this->title = $title; return $this; }
  public function setType ($type) { $type && is_string ($type) && in_array ($type, array ('string', 'color', 'date', 'list', 'pure', 'multi-datas', 'images')) && $this->type = $type; return $this; }
  public function b () {
    return '<b>' . $this->title . '</b>';
  }

  public function toString () {
    $return = '';

    if ($this->type === null)
      return $return;

    if ( $this->format === null & is_callable ($this->func) ) {
      $func = $this->func;
      $this->format = $func($this->obj);
    }

    $return .= '<div>';
      $return .= $this->b ();
      $return .= '<div class="'.$this->type.'">' . $this->format . '</div>';
    $return .= '</div>';

    return $return;
  }
}
class ShowText extends ShowRow {
  public function __construct () {
    parent::__construct ();
    $this->setType ('string');
  }

  public function setItem($text) {
    is_callable ($this->func) && $func = $this->func;
    $this->format .= $func( $text );
    return $this;
  }
}
class ShowColor extends ShowRow {
  public function __construct () {
    parent::__construct ();
    $this->setType ('color');
  }

  public function setItem($color) {
    is_callable ($this->func) && $func = $this->func;
    $this->format .= $func($color);
    return $this;
  }
}
class ShowDate extends ShowRow {
  public function __construct () {
    parent::__construct ();
    $this->setType ('date');
  }

  public function setItem($date) {
    is_callable ($this->func) && $func = $this->func;
    $this->format .= $func( $date );
    return $this;
  }
}

class ShowList extends ShowRow {
  public function __construct () {
    parent::__construct ();
    $this->setType ('list');
  }

  public function setItem($lists) {
    if( !is_array($lists) && !is_object($lists) )
      return $this;

    is_callable ($this->func) && $func = $this->func;
    foreach ( $lists as $list ) {
      $list = $func($list);
      $this->format .= !empty($list) ? '<a>' . $list . '</a>' : '';
    }
    return $this;
  }
}
class ShowPure extends ShowRow {
  public function __construct () {
    parent::__construct ();
    $this->setType ('pure');
  }

  public function setItem($pures) {
    if( !is_array($pures) && !is_object($pures) )
      return $this;

    is_callable ($this->func) && $func = $this->func;
    foreach ( $pures as $pure ) {
      $pure = $func($pure);
      $this->format .= !empty($pure) ? $pure . '<br>' : '';
    }
    return $this;
  }
}
class ShowMulti extends ShowRow {
  public function __construct () {
    parent::__construct ();
    $this->setType ('multi-datas');
  }

  public function setItem($multis) {
    if( !is_array($multis) && !is_object($multis) )
      return $this;

    is_callable ($this->func) && $func = $this->func;
    foreach ( $multis as $multi ) {
      $multi = $func($multi);
      $this->format .= !empty($multi) ? '<div title="' . $multi['title'] . '">' . $multi['content'] . '</div>' : '';
    }
    return $this;
  }
}
class ShowImages extends ShowRow {
  public function __construct () {
    parent::__construct ();
    $this->setType ('images');
  }

  public function setItem($images) {
    if( ( !is_array($images) && !is_object($images) ) || empty($images) ) {
      $this->format = '';
      return $this;
    }

    is_callable ($this->func) && $func = $this->func;
    foreach ( $images as $image ) {
      $image = $func($image);
      $this->format .= !empty($image) ? '<img src="' . $image . '"/>' : '';
    }
    return $this;
  }
}
