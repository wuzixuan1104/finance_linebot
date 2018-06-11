<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */


class BankProcess {
  public function __construct() {

  }

  public static function setBank($params) {

    Log::info( json_encode($params) );
    Log::info( __METHOD__);

    return true;
  }
}
