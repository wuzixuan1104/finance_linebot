<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Auth extends SiteController {

  public function __construct () {
    parent::__construct ();
  }

  public function verify () {
    $id = Input::get ('id');
    $token = Input::get ('code');

    if (!(($user = User::find ('one', array ('where' => array ('id = ?', $id)))) && ($active = UserActive::find ('one', array ('where' => array ('user_id = ? AND token = ?', $id, $token))))))
      return refresh (URL::base (''), 'flash', array ('type' => 'failure', 'msg' => '驗證失敗！', 'params' => array ()));

    $user->active = User::ACTIVE_ON;
    $user->save ();

    $asset = Asset::create (1)
                  ->addCSS ('/assets/css/site/Auth.css');

    return View::create ('site/Auth/verify.php')
                   ->with ('asset', $asset)
                   ->get ();
  }
}
