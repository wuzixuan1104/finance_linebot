<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListImages extends AdminListUnit {
  public function content($contents) {
    parent::content(implode('', array_filter(array_map(function($content) {
      $content instanceof \M\ImageUploader && $content = $content->url();
      return $content ? '<img src="' . $content . '" />' : '';
    }, $contents))));

    return $this->className('oaips')->width(50);
  }
}