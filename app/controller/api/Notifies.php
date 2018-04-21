<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Notifies extends ApiController {

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
   * @apiIgnore Not finished Method
   *
   * @apiName Notify
   * @/apiapi {get} /notify 取得通知
   * @apiGroup Notify
   * @apiDescription 通知
   *
   * @apiHeader {string} token 登入後的 Access Token
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id 通知ID
   * @apiSuccess {Number} brand_id 品牌ID
   * @apiSuccess {Number} user.id 使用者ID
   * @apiSuccess {Object} send 發送者
   * @apiSuccess {Number} send.id 發送者ID
   * @apiSuccess {Number} send.name 發送者名稱
   * @apiSuccess {Object} brand 品牌
   * @apiSuccess {Number} brand.id 品牌ID
   * @apiSuccess {Number} brand.name 品牌名稱
   * @apiSuccess {String} content 內容
   * @apiSuccess {String} read 已讀狀態 (已讀: yes, 未讀: no)
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *        [
   *           {
   *              "id" : "1",
   *              "user_id" : "",
   *              "send" : {
   *                "id" : "1",
   *                "name" : "1",
   *              }
   *              "content" : "您已在ooo編輯",
   *              "read" : "false",
   *              "created_at" : "2018-01-01 12:00:00",
   *              "updated_at" : "2018-01-01 12:00:00",
   *           },
   *           {
   *              "id" : "4",
   *              "user_id" : "",
   *              "brand" : {
   *                "id" : "1",
   *                "name" : "1",
   *              }
   *              "content" : "您已在ooo投稿",
   *              "read" : "true",
   *              "created_at" : "2018-01-01 12:00:00",
   *              "updated_at" : "2018-01-01 12:00:00",
   *           }
   *       ]
   *
   * @apiUse MyError
   */

  public function index() {
    $validation = function(&$gets) {
      Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
      Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);
    };

    $gets = Input::get ();

    if( $error = Validation::form($validation, $gets) )
      return Output::json($error, 400);

    $notifies = array_map (function ($notify) {
      $notify = array (
          'id' => $notify->id,
          'user_id' => $notify->user_id,
          'brand' => ( $notify->brand_id != 0 ) ? array(
            'id' => $notify->brand_id,
            'name' => $notify->brand->name,
          ) : array(),
          'send' => ( $notify->send_id != 0 ) ? array(
            'id' => $notify->send_id,
            'name' => User::find_by_id($notify->send_id)->name,
          ) : array(),
          'content' => $notify->content,
          'read' => ($notify->read == Notify::READ_YES) ? true : false,
          'created_at' => $notify->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $notify->updated_at->format ('Y-m-d H:i:s'),
        );

      if(empty($notify['brand']))
        unset($notify['brand']);

      if(empty($notify['send']))
        unset($notify['send']);

      return $notify;
    }, Notify::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'], 'where' => array('user_id = ?', $this->user->id) )));

    $notifies = array_values( array_filter($notifies) );
    return Output::json($notifies);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName Create
   * @api {post} /api/notify 新增通知
   * @apiGroup Notify
   * @apiDescription 新增通知
   * @apiHeader {string} token 登入後的 Access Token
   * @apiParam {Number} brand_id 品牌ID
   * @apiParam {Number} user_id 接收者ID
   * @apiParam {String} content 內容
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function create() {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'brand_id', '品牌ID', 0)->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'user_id', '接收者ID', 0)->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'content', '信箱')->isStringOrNumber ()->doTrim ();

      !$posts['brand_id'] && !$posts['user_id'] && Validation::error('至少傳入品牌ID或接收者ID');

      if($posts['brand_id'] && !Brand::find_by_id($posts['brand_id']))
        Validation::error('查無此品牌');

      if($posts['user_id'] && !User::find_by_id($posts['user_id']))
        Validation::error('查無此使用者');

      $posts = array_merge($posts, array('send_id' => $this->user->id, 'read' => Notify::READ_NO));
    };

    $transaction = function ($posts) {
      return Notify::create($posts);
     };

    $posts = Input::post();

    if ($error = Validation::form ($validation, $posts))
      return Output::json($error, 400);

    if ($error = Brand::getTransactionError ($transaction, $posts))
      return Output::json($error, 400);

    return Output::json(['message' => "成功"], 200);
   }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName Update
   * @api {put} /api/notify 編輯通知
   * @apiGroup Notify
   * @apiDescription 編輯通知
   *
   * @apiParam {Number} id 通知ID
   * @apiParam {String} read 已讀狀態 (已讀: yes, 未讀: no)
   * @apiParam {String} content 內容
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function update() {
    $validation = function(&$posts, &$notify) {
      Validation::need($posts, 'id', '通知ID')->isStringOrNumber ()->doTrim ()->length(1, 11);
      Validation::maybe ($posts, 'content', '內容')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'read', '已讀狀態')->isStringOrNumber ()->doTrim ()->length(1, 50);

    if( !isset($posts['content']) && !isset($posts['read']))
      Validation::error('傳入參更新參數錯誤！');

    if( !$notify = Notify::find_by_id($posts['id']) )
      Validation::error('查無此通知！！');

    if( isset($posts['read']) && !array_key_exists($posts['read'], Notify::$readTexts) )
      Validation::error('已讀狀態傳入值有誤！！');

    };

    $transaction = function($posts, $notify) {
      return $notify->columnsUpdate ($posts) && $notify->save ();
    };

    $posts = Input::put(null, Input::PUT_FORM_DATA);

    if ($error = Validation::form ($validation, $posts, $notify))
      return Output::json($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $notify))
      return Output::json($error, 400);

    return Output::json(['message' => '成功'], 200);
  }

}
