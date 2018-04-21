<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Platform extends Controller {

  public function __construct () {
    parent::__construct ();
  }

  public function logout () {
    Session::unsetData ('token');
    return refresh (URL::base ('admin/login'), 'flash', array ('type' => 'success', 'msg' => '登出成功！', 'params' => array ()));
  }

  public function login () {
    if (@Admin::current ()->id)
      return refresh (URL::base ('admin'));

    $asset = Asset::create ()
                  ->addCSS ('/assets/css/icon-login.css')
                  ->addCSS ('/assets/css/admin/login.css')
                  ->addJS ('/assets/js/res/jquery-1.10.2.min.js')
                  ->addJS ('/assets/js/login.js');

    $flash = Session::getFlashData ('flash');

    return View::create ('admin/Platform/login.php')
               ->with ('asset', $asset)
               ->with ('flash', $flash)
               ->with ('params', $flash['params'])
               ->output ();
  }

  public function acSignin () {
    $validation = function (&$posts, &$admin) {
      Validation::need ($posts, 'account', '帳號')->isStringOrNumber ()->doTrim ()->length (1, 255);
      Validation::need ($posts, 'password', '密碼')->isStringOrNumber ()->doTrim ()->length (1, 255);

      if (!$admin = Admin::find ('one', array ('select' => 'id, account, password, token', 'where' => array ('account = ?', $posts['account']))))
        Validation::error ('此帳號不存在！');

      if (!password_verify ($posts['password'], $admin->password))
        Validation::error ('密碼錯誤！');
    };

    $transaction = function ($admin) {
      $admin->token || $admin->token = md5 (($admin->id ? $admin->id . '_' : '') . uniqid (rand () . '_'));
      return $admin->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts, $admin))
      return refresh (URL::base ('admin/login'), 'flash', array ('type' => 'failure', 'msg' => $error, 'params' => $posts));

    if ($error = Admin::getTransactionError ($transaction, $admin))
      return refresh (URL::base ('admin/login'), 'flash', array ('type' => 'failure', 'msg' => $error, 'params' => $posts));

    Session::setData ('token', $admin->token);
    return refresh (URL::base ('admin'), 'flash', array ('type' => 'success', 'msg' => '登入成功！', 'params' => array ()));
  }
}
