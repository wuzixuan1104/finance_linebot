<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminListLinks extends AdminListUnit {
  public function content($content) {
    parent::content(implode('', array_map(function($t) {
      if (is_array($t))
        return '<a href="' . array_shift($t) . '">' . array_shift($t) . '</a>';
    
      if ($t instanceof Hyperlink)
        return $t;
      
      return '<a href="' . $t . '">' . $t . '</a>';
    }, $content)));
    return $this->className('links');
  }
}