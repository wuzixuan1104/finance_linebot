<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Auth extends ApiController {

  public function __construct () {
    parent::__construct ();
  }

  /**
   * @apiDefine MyError 錯誤訊息
   *
   * @apiError {String} message  訊息
   * @apiErrorExample {json} 錯誤:
   *     HTTP/1.1 400 Error
   *     {
   *       "message": "錯誤訊息..",
   *     }
   */

  /**
   * @apiDefine MySuccess 成功訊息
   *
   * @apiSuccess {String} message  訊息
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *       "message": "成功訊息..",
   *     }
   */

  /**
   * @apiDefine login 需先登入
   * 此 API 必需先登入後取得 <code>Access Token</code>，在藉由 Header 夾帶 Access Token 驗證身份
   *
   * @apiHeader {string} token 登入後的 Access Token
   */

  /**
   * @apiDefine loginMaybe 須不須登入皆可
   * 此 API 若有帶 <code>Access Token</code> 則代表登入
   *
   * @apiHeader {string} [token] 登入後的 Access Token
   */



  /**
   *
   * @apiName Login
   *
   * @api {post} /api/login 登入
   * @apiGroup Auth
   * @apiDescription 登入系統
   *
   * @apiParam {String} email  帳號
   * @apiParam {String} password 密碼
   *
   * @apiSuccess {String} token Access Token
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *       "token": "a0b1c2d3e4f5g6h7i8j9k",
   *     }
   *
   * @apiUse MyError
   */

  public function login () {

    $validation = function(&$posts, &$user) {
      Validation::need ($posts, 'email', 'E-Mail')->isEmail ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);
      Validation::need ($posts, 'password', '密碼')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);

      if( !$user = User::find( 'one', array('select' => 'id, email, password, active, access_token', 'where' => array('email = ? AND password != ? AND facebook_id = ? AND google_id = ?', $posts['email'], '', '', '') ) ) )
        Validation::error ('此帳號不存在！');

      if (!password_verify ($posts['password'], $user->password))
        Validation::error ('密碼錯誤！');

      if ( $user->active != User::ACTIVE_ON )
        Validation::error ('此信箱尚未激活！');
    };

    $transaction = function ($user) {
      $user->access_token || $user->access_token = md5 (($user->id ? $user->id . '_' : '') . uniqid (rand () . '_'));
      return $user->save ();
    };

    $posts = Input::post();

    if( $error = Validation::form($validation, $posts, $user) )
      return Output::json($error, 400);

    if ($error = User::getTransactionError ($transaction, $user))
      return Output::json($error, 400);

    return Output::json(['token' => $user->access_token], 200);
  }

  /**
   * @apiName FacebookLogin
   * @api {post} /api/login/facebook FB登入
   * @apiGroup Auth
   * @apiDescription 登入系統
   *
   * @apiParam {String} facebook_id  臉書 ID
   * @apiParam {String} email        帳號
   * @apiParam {String} name         名稱
   *
   * @apiSuccess {String} token Access Token
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *       "token": "a0b1c2d3e4f5g6h7i8j9k",
   *     }
   *
   * @apiUse MyError
   */

  public function facebookLogin () {
    $validation = function(&$posts) {
      Validation::need ($posts, 'facebook_id', '臉書 ID')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);
      Validation::need ($posts, 'email', 'E-Mail')->isEmail ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);
      Validation::need ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);

      $posts = array_merge (array (
          'password' => '',
          'access_token' => '',
          'google_id' => '',
          'avatar' => '',
          'city' => '',
          'phone' => '',
          'brief' => '',
          'expertise' => '',
          'birthday' => null,
          'active' => User::ACTIVE_ON,
        ), $posts);
    };

    $transaction = function (&$posts, &$user) {
      if (!$user = User::find ('one', Where::create ('facebook_id = ?', $posts['facebook_id'])))
        if (!$user = User::create ($posts))
          return false;

      $user->access_token || $user->access_token = md5 (($user->id ? $user->id . '_' : '') . uniqid (rand () . '_'));
      return $user->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $user))
      return Output::json ($error, 400);

    return Output::json(['token' => $user->access_token], 200);
  }

  /**
   * @apiName GoogleLogin
   * @api {post} /api/login/google Google登入
   * @apiGroup Auth
   * @apiDescription 登入系統
   *
   * @apiParam {String} google_id    Google ID
   * @apiParam {String} email        帳號
   * @apiParam {String} name         名稱
   *
   * @apiSuccess {String} token Access Token
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *       "token": "a0b1c2d3e4f5g6h7i8j9k",
   *     }
   *
   * @apiUse MyError
   */

  public function googleLogin () {
    $validation = function(&$posts) {
      Validation::need ($posts, 'google_id', '臉書 ID')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);
      Validation::need ($posts, 'email', 'E-Mail')->isEmail ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);
      Validation::need ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);

      $posts = array_merge (array (
          'password' => '',
          'access_token' => '',
          'facebook_id' => '',
          'avatar' => '',
          'city' => '',
          'phone' => '',
          'brief' => '',
          'expertise' => '',
          'birthday' => null,
          'active' => User::ACTIVE_ON,
        ), $posts);
    };

    $transaction = function (&$posts, &$user) {
      if (!$user = User::find ('one', Where::create ('google_id = ?', $posts['google_id'])))
        if (!$user = User::create ($posts))
          return false;

      $user->access_token || $user->access_token = md5 (($user->id ? $user->id . '_' : '') . uniqid (rand () . '_'));
      return $user->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $user))
      return Output::json ($error, 400);

    return Output::json(['token' => $user->access_token], 200);
  }

  /**
   * @apiName Register
   *
   * @api {post} /api/register 註冊
   * @apiGroup Auth
   * @apiDescription 註冊會員
   *
   * @apiParam {String} email    帳號
   * @apiParam {String} password   密碼
   * @apiParam {String} name       名稱
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function register () {
    $validation = function(&$posts, &$user) {
      Validation::need ($posts, 'email', 'E-Mail')->isEmail ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);
      Validation::need ($posts, 'password', '密碼')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);
      Validation::need ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);

      if ($user = User::find ('one', array ('select' => 'id, email, password, access_token', 'where' => array ('email = ? AND password != ? AND facebook_id = ? AND google_id = ?', $posts['email'], '', '', '') ) ) )
        Validation::error ('帳號已存在！');

      $posts['password'] = password_hash ($posts['password'], PASSWORD_DEFAULT);
    };

    $transaction = function ($posts, &$user, &$active) {
      if (!$user = User::create ($posts))
        return false;

      $user->access_token || $user->access_token = md5 (($user->id ? $user->id . '_' : '') . uniqid (rand () . '_'));
      if( !$user->save () )
        return false;

      return $active = UserActive::create (array (
          'user_id' => $user->id,
          'token' => md5 (uniqid (mt_rand (), true))
        ));
    };

    $posts = Input::post();

    if( $error = Validation::form($validation, $posts, $user) )
      return Output::json($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $user, $active))
      return Output::json($error, 400);

    $mainContent = View::create ('mail/UserActive.php')
                   ->with ('user', $user)
                   ->with ('active', $active)
                   ->get ();

    Load::lib ('Mail.php');
    $mail = Mail::create ()->setSubject ('ADPOST 註冊會員')->setBody ($mainContent);
    $mail->addTo ($user->email, $user->name);
    $mail->send ();

    return Output::json(['message' => "成功"], 200);
  }

  /**
   * @apiName Forget
   *
   * @api {post} /api/forget 忘記密碼
   * @apiGroup Auth
   * @apiDescription 忘記密碼
   *
   * @apiParam {String} email    帳號
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function forget () {
    $validation = function(&$posts, &$user) {
      Validation::need($posts, 'email')->isEmail ()->doTrim ()->doRemoveHtmlTags ()->length (1, 191);

      if (!$user = User::find ('one', array ('select' => 'id, email, name', 'where' => array ('email = ?', $posts['email']))))
        Validation::error ('此 E-Mail 不存在！');
    };

    $transaction = function ($posts, &$user, &$active) {
      return $active = UserActive::create (array (
          'user_id' => $user->id,
          'token' => md5 (uniqid (mt_rand (), true))
        ));
    };

    $posts = Input::post();

    if( $error = Validation::form($validation, $posts, $user) )
      return Output::json($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $user, $active))
      return Output::json($error, 400);

    $mainContent = View::create ('mail/UserForgot.php')
                   ->with ('user', $user)
                   ->with ('active', $active)
                   ->get ();

    Load::lib ('Mail.php');
    $mail = Mail::create ()->setSubject ('ADPOST 忘記密碼')->setBody ($mainContent);
    $mail->addTo ($user->email, $user->name);
    $mail->send ();

    return Output::json(['message' => "成功"], 200);
  }

}
