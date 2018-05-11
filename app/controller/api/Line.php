<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Line extends ApiController {
  public function __construct() {
    parent::__construct();
  }

  public function index() {
    Load::lib ('MyLineBot.php');
    $events = MyLineBot::events();

    foreach( $events as $event ) {
      $user = $this->checkUserExist( $event->getUserId() );

    }
  }

  public function checkUserExist($uid) {
    if( !$obj = User::find_by_uid($uid) )
      if( !$obj = User::create( array('uid' => $uid) ) )
        return false;
    return $obj;
  }
}
