<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Banks extends ApiLoginController {

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
    * @apiName Bank
    * @api {get} /api/bank 取得銀行資訊
    * @apiGroup Bank
    * @apiDescription 銀行資訊
    *
    * @apiHeader {string} token 登入後的 Access Token
    * @apiParam {Number} [offset=0]      位移
    * @apiParam {String} [limit=20]      長度
    *
    * @apiSuccess {Number} id 銀行ID
    * @apiSuccess {String} name 名稱
    * @apiSuccess {String} code 銀行代號
    * @apiSuccess {DateTime} created_at 建立時間
    * @apiSuccess {DateTime} updated_at 更新時間
    *
    * @apiSuccessExample {json} 成功:
    *     HTTP/1.1 200 OK
    *     [
    *       {
    *         "id" : "1",
    *         "name" : "test",
    *         "code" : "13425547777776",
    *         "created_at" : "2018-04-16 12:00:00",
    *         "updated_at" : "2018-04-16 12:00:00",
    *       }
    *      ]
    *
    * @apiUse MyError
    */

    public function index() {
      $validation = function(&$gets, &$obj) {
        Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
        Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);
        if( !$obj = Bank::find('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'] ) ) )
          Validation::error('查無銀行資訊！');
      };

      $gets = Input::get();

      if ( $error = Validation::form ($validation, $gets, $obj) )
        return Output::json($error, 400);

      $banks = array_map (function ($bank) {
        $bank = array(
          'id' => $bank->id,
          'name' => $bank->name,
          'code' => $bank->code,
          'created_at' => $bank->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $bank->updated_at->format ('Y-m-d H:i:s'),
        );
        return $bank;
      }, $obj);

      return Output::json($banks);
    }
}
