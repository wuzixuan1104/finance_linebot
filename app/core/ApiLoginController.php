<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

abstract class ApiLoginController extends ApiController {
  public function __construct () {
    parent::__construct ();

    if (!$this->user)
      return $this->constructError (Output::json('請先登入！', 400));
  }
}
