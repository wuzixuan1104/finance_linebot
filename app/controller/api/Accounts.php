<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Accounts extends ApiLoginController {

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
    * @apiName Account
    * @api {get} /api/account 取得帳戶資訊
    * @apiGroup Account
    * @apiDescription 帳戶資訊
    *
    * @apiHeader {string} token 登入後的 Access Token
    * @apiParam {Number} [offset=0]      位移
    * @apiParam {String} [limit=20]      長度
    *
    * @apiSuccess {Number} id 帳戶ID
    * @apiSuccess {Object} user 使用者
    * @apiSuccess {String} user.id 使用者ID
    * @apiSuccess {String} user.name 使用者名稱
    * @apiSuccess {String} name 名稱
    * @apiSuccess {Object} bank 銀行
    * @apiSuccess {String} bank.id 銀行ID
    * @apiSuccess {String} bank.code 銀行代號
    * @apiSuccess {String} bank.name 銀行名稱
    * @apiSuccess {String} bank.branch 銀行分行
    * @apiSuccess {String} bank.account 銀行帳戶
    * @apiSuccess {String} phone 電話
    * @apiSuccess {DateTime} created_at 建立時間
    * @apiSuccess {DateTime} updated_at 更新時間
    *
    * @apiSuccessExample {json} 成功:
    *     HTTP/1.1 200 OK
    *     [
    *       {
    *         "id" : "1",
    *         "user" : {
    *           "id" : "1",
    *           "name" : "cherry",
    *         }
    *         "bank" : {
    *           "id" : "1",
    *           "name" : "tamsui",
    *           "code" : "tamsuiasdafere",
    *           "branch" : "tamsui",
    *           "account" : "cherry",
    *         }
    *         "name" : "cherry",
    *         "phone" : "xxxxxxxxxx.png",
    *         "created_at" : "2018-04-16 12:00:00",
    *         "updated_at" : "2018-04-16 12:00:00",
    *       }
    *     ]
    *
    * @apiUse MyError
    */

    public function index() {
      $validation = function(&$gets, &$obj) {
        Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
        Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);
        if( !$obj = Account::find('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'], 'where' => array('user_id' => $this->user->id) ) ) )
          Validation::error('查無帳戶資訊！');
      };

      $gets = Input::get();

      if ( $error = Validation::form ($validation, $gets, $obj) )
        return Output::json($error, 400);

      $accounts = array_map (function ($account) {
        $account = array(
          'id' => $account->id,
          'user' => array(
            'id' => $account->user_id,
            'name' => $account->user->name,
          ),
          'bank' => array(
            'id' => $account->bank_id,
            'code' => $account->bank->code,
            'name' => $account->bank->name,
            'branch' => $account->bank_branch,
            'account' => $account->bank_account,
          ),
          'name' => $account->name,
          'phone' => $account->phone,
          'created_at' => $account->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $account->updated_at->format ('Y-m-d H:i:s'),
        );

        return $account;
      }, $obj);

      return Output::json($accounts);
    }

    /**
    *
    * @apiIgnore Not finished Method
    *
    * @apiName UpdateAccount
    * @api {put} /api/account 編輯帳戶資料
    * @apiGroup Account
    * @apiDescription 帳戶資料
    *
    * @apiHeader {string} token 登入後的 Access Token
    *
    * @apiParam {Number} id 帳戶ID
    * @apiParam {String} name 名稱
    * @apiParam {Number} bank_id 銀行ID
    * @apiParam {String} bank_branch 銀行分行
    * @apiParam {String} bank_account 銀行帳戶
    * @apiParam {String} phone 電話
    *
    * @apiUse MySuccess
    * @apiUse MyError
    */

    public function update() {
      $validation = function(&$posts, &$obj) {
        Validation::need ($posts, 'id', '帳戶ID')->isNumber ()->doTrim ()->length(1, 11);
        Validation::maybe ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->length(1, 191);
        Validation::maybe ($posts, 'bank_id', '銀行ID')->isStringOrNumber ()->doTrim ()->length(1, 11);
        Validation::maybe ($posts, 'bank_branch', '銀行分行')->isStringOrNumber ()->doTrim ()->length(1, 191);
        Validation::maybe ($posts, 'bank_account', '銀行帳戶')->isStringOrNumber ()->doTrim ()->length(1, 191);
        Validation::maybe ($posts, 'phone', '電話')->isStringOrNumber ()->doTrim ()->length(1, 191);

        if( !$obj = Account::find_by_id($posts['id']) )
          Validation::error('查無帳號資訊！');

        if( isset($posts['bank_id']) && !Bank::find_by_id($posts['bank_id']) )
          Validation::error('查無此銀行ID');
      };

      $transaction = function($posts, $obj) {
        return $obj->columnsUpdate ($posts) && $obj->save ();
      };

      $posts = Input::put(null, Input::PUT_FORM_DATA);

      if ($error = Validation::form ($validation, $posts, $obj))
        return Output::json($error, 400);

      if ($error = Account::getTransactionError ($transaction, $posts, $obj))
        return Output::json($error, 400);

      return Output::json(['message' => '成功'], 200);
    }

    /**
     *
     * @apiIgnore Not finished Method
     *
     * @apiName CreateAccount
     * @api {post} /api /account 新增帳戶
     * @apiGroup Account
     * @apiDescription 新增帳戶
     * @apiHeader {string} token 登入後的 Access Token
     *
     * @apiParam {Number} user_id 使用者ID
     * @apiParam {String} name 品牌名稱
     * @apiParam {String} tax_number 統一編號
     * @apiParam {String} email 信箱
     * @apiParam {String} phone 電話
     * @apiParam {String} company_name 公司名稱
     * @apiParam {String} company_city 公司城市
     * @apiParam {String} company_area 公司區域
     * @apiParam {String} company_address 公司地址
     * @apiParam {String} website 網站
     * @apiParam {String} description 簡述
     *
     * @apiUse MySuccess
     * @apiUse MyError
     */

    public function create() {
      $validation = function (&$posts) {
        Validation::need ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->length(1, 191);
        Validation::need ($posts, 'bank_id', '銀行ID')->isNumber ()->doTrim ()->length(1, 11);
        Validation::need ($posts, 'bank_branch', '銀行分行')->isStringOrNumber ()->doTrim ()->length(1, 191);
        Validation::need ($posts, 'bank_account', '銀行帳號')->isStringOrNumber ()->doTrim ()->length(1, 191);
        Validation::need ($posts, 'phone', '電話')->isStringOrNumber ()->doTrim ()->length(1, 191);

        if( !Bank::find_by_id($posts['bank_id']) )
          Validation::error('查無此銀行！');
      };

      $transaction = function ($posts) {
        return $obj = Account::create ( array_merge( $posts, array('user_id' => $this->user->id) ) );
      };

      $posts = Input::post();

      if ($error = Validation::form ($validation, $posts))
        return Output::json($error, 400);

      if ($error = Brand::getTransactionError ($transaction, $posts))
        return Output::json($error, 400);

      return Output::json(['message' => "成功"], 200);
    }

}
