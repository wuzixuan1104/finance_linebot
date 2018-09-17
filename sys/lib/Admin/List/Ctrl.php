<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListCtrl extends AdminListUnit {
  private $ctrls = [];

  private function update($title) {
    $width = 14 + (count($this->ctrls) - 1) * 22 + 4 * 2 + 2 * 2;
    $this->className('ctrls');
    $this->title(count($this->ctrls) > 1 ? '編輯' : $title);
    $this->width($width < 44 ? 44 : $width);
    return $this;
  }

  public function addShow($name) {
    array_unshift($this->ctrls, call_user_func_array('Url::toRouterHyperlink', func_get_args())->className('icon-29'));
    return $this->update('檢視');
  }

  public function addEdit($name) {
    array_unshift($this->ctrls, call_user_func_array('Url::toRouterHyperlink', func_get_args())->className('icon-03'));
    return $this->update('修改');
  }

  public function addDelete($name) {
    array_unshift($this->ctrls, call_user_func_array('Url::toRouterHyperlink', func_get_args())->className('icon-04')->attrs(['data-method' =>'delete']));
    return $this->update('刪除');
  }

  public function add($hyperlink) {
    array_unshift($this->ctrls, $hyperlink);
    return $this->update('編輯');
  }

  public function getContent() {
    return implode('', $this->ctrls);
  }
}