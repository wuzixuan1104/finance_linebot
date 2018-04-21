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
   * @apiName Notify
   * @api {get} /notify 取得通知
   * @apiGroup Notify
   * @apiDescription 通知
   *
   * @apiHeader {string} token 登入後的 Access Token
   * @apiParam {Number} id 使用者ID
   * @apiParam {String} type 通知類型 (user, brand)
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id 通知ID
   * @apiSuccess {Number} user_id 使用者ID
   * @apiSuccess {Number} send_id 發送者ID
   * @apiSuccess {String} content 內容
   * @apiSuccess {String} read 已讀狀態 (已讀: yes, 未讀: no)
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *        [
   *           {
   *              "id" : "1",
   *              "user_id" : "1",
   *              "send_id" : "3",
   *              "content" : "您已在ooo編輯",
   *              "read" : "no",
   *              "created_at" : "2018-01-01 12:00:00",
   *              "updated_at" : "2018-01-01 12:00:00",
   *           },
   *           {
   *              "id" : "4",
   *              "user_id" : "1",
   *              "send_id" : "7",
   *              "content" : "您已在ooo投稿",
   *              "read" : "yes",
   *              "created_at" : "2018-01-01 12:00:00",
   *              "updated_at" : "2018-01-01 12:00:00",
   *           }
   *       ]
   *     }
   *
   * @apiUse MyError
   */

  public function notify() {

  }

  /**
   * @apiName Create
   * @api {post} /notify 新增通知
   * @apiGroup Notify
   * @apiDescription 新增通知
   * @apiHeader {string} token 登入後的 Access Token
   * @apiParam {Number} user_id 使用者ID
   * @apiParam {Number} send_id 發送者ID
   * @apiParam {String} read 已讀狀態 (已讀: yes, 未讀: no)
   * @apiParam {String} content 內容
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function create() {

  }

  /**
   * @apiName Update
   * @api {post} /notify 編輯通知
   * @apiGroup Notify
   * @apiDescription 編輯通知
   *
   * @apiParam {Number} id 通知ID
   * @apiParam {String} read 已讀狀態 (已讀: yes, 未讀: no)
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function update() {

  }

}
