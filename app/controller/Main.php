<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Main extends SiteController {

  public function a () {
    $posts = Input::post ();
    
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($posts);
    exit ();
  }
  public function index () {
    return View::create ('test.php');
  }



  public function admin () {
    return refresh (Url::base ('admin/login'));
  }
}
