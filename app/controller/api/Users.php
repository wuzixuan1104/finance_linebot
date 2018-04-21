<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Users extends ApiLoginController {

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
   * @apiName User
   * @api {get} /user 取得個人資訊
   * @apiGroup User
   * @apiDescription 使用者資訊
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiSuccess {Number} id 使用者ID
   * @apiSuccess {String} name 名稱
   * @apiSuccess {String} avatar 頭像
   * @apiSuccess {String} city 城市
   * @apiSuccess {String} brief 簡介
   * @apiSuccess {String} phone 電話
   * @apiSuccess {String} email 信箱
   * @apiSuccess {Date} birthday 生日
   * @apiSuccess {String} expertise 專長
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *       "id" : "1",
   *       "name" : "cherry",
   *       "avatar" : "xxxxxxxxxx.png",
   *       "city" : "Tamsui",
   *       "brief" : "我好棒棒",
   *       "phone" : "0900000000",
   *       "email" : "cherry@adpost.com.tw",
   *       "birthday" : "2018-04-01",
   *       "expertise" : "專長",
   *       "created_at" : "2018-04-16 12:00:00",
   *       "updated_at" : "2018-04-16 12:00:00",
   *     }
   *
   * @apiUse MyError
   */

  public function index() {
    $return = array (
        'id' => $this->user->id,
        'name' => $this->user->name,
        'email' => $this->user->email,
        'avartar' => $this->user->avatar->url(),
        'city' => $this->user->city,
        'phone' => $this->user->phone,
        'brief' => $this->user->brief,
        'expertise' => $this->user->expertise,
        'birthday' => !empty($this->user->birthday) ? $this->user->birthday->format('Y-m-d H:i:s') : '',
        'created_at' => $this->user->created_at->format ('Y-m-d H:i:s'),
        'updated_at' => $this->user->updated_at->format ('Y-m-d H:i:s'),
    );
    return Output::json($return);
  }

  /**
   * @apiName Update
   * @api {post} /user 編輯個人資料
   * @apiGroup User
   * @apiDescription 個人資料
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {String} [name] 名稱
   * @apiParam {String} [password] 密碼
   * @apiParam {String} [avatar] 頭像
   * @apiParam {String} [city] 城市
   * @apiParam {String} [brief] 簡介
   * @apiParam {String} [phone] 電話
   * @apiParam {Date} [birthday] 生日
   * @apiParam {String} [expertise] 專長
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function update() {
    $validation = function(&$posts, $files) {
      Validation::maybe ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->length(1, 191);
      Validation::maybe ($posts, 'password', '密碼', '')->isStringOrNumber ()->doTrim ()->length(1, 191);
      Validation::maybe ($files, 'avatar', '圖片', array() )->isUploadFile ();
      Validation::maybe ($posts, 'city', '城市')->isStringOrNumber ()->doTrim ()->length(1, 50);
      Validation::maybe ($posts, 'brief', '簡介')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'phone', '電話')->isStringOrNumber ()->doTrim ()->length(1, 50);
      Validation::maybe ($posts, 'birthday', '生日')->isDate ()->doTrim ();
      Validation::maybe ($posts, 'expertise', '專長')->isStringOrNumber ()->doTrim ();

      if( $posts['password'] )
        $posts['password'] = password_hash ($posts['password'], PASSWORD_DEFAULT);
    };

    $transaction = function($posts, $files) {
      if( !($this->user->columnsUpdate ($posts) && $this->user->save ()) )
        return false;

      if( !empty($files['avatar']) && !($this->user->avatar->put($files['avatar']['tmp_name']) && $this->user->save() ) ) {
        return false;
      }
      return true;
    };

    $posts = Input::put(null, Input::PUT_FORM_DATA);
    $files = Input::file();

    if ($error = Validation::form ($validation, $posts, $files))
      return Output::json($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $files))
      return Output::json($error, 400);

    return Output::json(['message' => '成功'], 200);
  }

}
