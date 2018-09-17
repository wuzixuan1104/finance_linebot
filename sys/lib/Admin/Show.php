<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShow {
  private $obj, $back, $units = [];
    
  public static function create(\M\Model $obj = null) {
    return new static($obj);
  }

  public function __construct(\M\Model $obj = null) {
    $this->obj = $obj;
  }

  public function back() {
    return $this->back ? '<div class="back">' . $this->back . '</div>' : '';
  }

  public function setBackUrl($back, $text = '回列表') {
    $this->back = Hyperlink::create($back)->text($text)->className('icon-36');
    return $this;
  }

  public function panel($closure) {
    $this->units = [];
    $title = null;
    $closure($this->obj, $title);
    $title == null && $title = '詳細資料';

    $return  = '';

    if (!$this->units)
      return $return;

    $return .= '<span class="title">' . $title . '</span>';
    $return .= '<div class="panel show">' . implode('', $this->units) .'</div>';
    $this->units = [];
    return $return;
  }

  public function appendUnit(AdminShowUnit $unit) {
    array_push($this->units, $unit);
    return $this;
  }
}
