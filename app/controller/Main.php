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

  }
  public function index () {
    // $this->layout
    //      ->with ('current_url', URL::base (''));
    //
    // return $this->view->setPath ('site/index.php');
    PassbookRecord::find ('all');
  }
  public function admin () {
    return refresh (Url::base ('admin/login'));
  }
}
