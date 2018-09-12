<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Main extends Controller {
  public function index() {
    return View::create('site/Main/index.php');
  }
}
