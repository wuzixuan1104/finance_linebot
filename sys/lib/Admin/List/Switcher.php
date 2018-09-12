<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListSwitcher extends AdminListImages {
  private $on, $off, $url, $column, $cntLabel;

  private function update() {
    return $this->width(56)->className('center');
  }
  
  public function on($on) {
    $this->on = $on;
    return $this->update();
  }

  public function off($off) {
    $this->off = $off;
    return $this->update();
  }

  public function url($url) {
    $this->url = $url;
    return $this->update();
  }

  public function column($column) {
    $this->column = $column;
    return $this->update();
  }

  public function cntLabel($cntLabel) {
    $this->cntLabel = $cntLabel;
    return $this->update();
  }

  public function getContent() {
    $this->on !== null     || gg('AdminListSwitcher 未設定 ON 的值！');
    $this->off !== null    || gg('AdminListSwitcher 未設定 OFF 的值！');
    $this->url !== null    || gg('AdminListSwitcher 未設定 Ajax 時的 Url！');
    $this->column !== null || gg('AdminListSwitcher 未設定要變更的欄位名稱！');

    $attrs = [
      'class' => 'switch ajax',
      'data-url' => $this->url,
      'data-column' => $this->column,
      'data-true' => $this->on,
      'data-false' => $this->off,
    ];

    $this->column === null || $attrs['data-cntlabel'] = $this->cntLabel;

    $return = '';
    $return .= '<label ' . attr($attrs) . '>';
      $return .= '<input type="checkbox"' . ($this->obj->{$this->column} == $this->on ? ' checked' : '') . '/>';
      $return .= '<span></span>';
    $return .= '</label>';

    return $return;
  }
}