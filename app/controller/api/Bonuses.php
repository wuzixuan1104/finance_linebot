<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Bonuses extends ApiLoginController {

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
    * @apiName BonusAdv
    * @/apiapi {get} /bonus/advs 取得廣告獎金列表
    * @apiGroup Bonus
    * @apiDescription 廣告獎金列表
    *
    * @apiHeader {string} token 登入後的 Access Token
    * @apiParam {Number} [offset=0]      位移
    * @apiParam {String} [limit=20]      長度
    *
    * @apiSuccess {Number} id 獎金ID
    * @apiSuccess {Object} adv 廣告資訊
    * @apiSuccess {String} adv.id 廣告ID
    * @apiSuccess {String} adv.title 廣告標題
    * @apiSuccess {String} adv.description 廣告描述
    * @apiSuccess {String} adv.cnt_view 廣吿觀看次數
    * @apiSuccess {String} adv.price 廣告獎金
    * @apiSuccess {DateTime} created_at 建立時間
    * @apiSuccess {DateTime} updated_at 更新時間
    *
    * @apiSuccessExample {json} 成功:
    *     HTTP/1.1 200 OK
    *     [
    *       {
    *         "id" : "1",
    *         "adv" : {
    *           "id" : "1",
    *           "title" : "cherry",
    *           "description" : "test",
    *           "cnt_view" : "3",
    *           "price" : "1200",
    *         }
    *         "created_at" : "2018-04-16 12:00:00",
    *         "updated_at" : "2018-04-16 12:00:00",
    *       },
    *         "id" : "2",
    *         "adv" : {
    *           "id" : "2",
    *           "title" : "cherry",
    *           "description" : "test",
    *           "cnt_view" : "3",
    *           "price" : "1200",
    *         }
    *         "created_at" : "2018-04-16 12:00:00",
    *         "updated_at" : "2018-04-16 12:00:00",
    *       }
    *     ]
    *
    * @apiUse MyError
    */

    public function advs() {
      $validation = function(&$gets) {
        Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
        Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);
      };

      $gets = Input::get();

      if ( $error = Validation::form ($validation, $gets) )
        return Output::json($error, 400);

      if ( !$obj = Bonus::find('all', array('where' => array('user_id = ?', $this->user->id ) )) )
        return Ouput::json('查無獎金資料！', 400);

      $bonuses = array_map (function ($bonus) {
        $bonus = array(
          'id' => $bonus->id,
          'adv' => array(
            'id' => $bonus->adv->id,
            'title' => $bonus->adv->title,
            'description' => $bonus->adv->description,
            'cnt_view' => $bonus->adv->cnt_view,
            'price' => $bonus->remain_price,
          ),
          'created_at' => $bonus->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $bonus->updated_at->format ('Y-m-d H:i:s'),
        );

        return $bonus;
      }, $obj);

      return Output::json($bonuses);
    }

    /**
     *
     * @apiIgnore Not finished Method
     *
     * @apiName BonusAmount
     * @api {get} /api/bonus/amount 取得獎金統計
     * @apiGroup Bonus
     * @apiDescription 獎金統計
     *
     * @apiHeader {string} token 登入後的 Access Token
     * @apiParam {Number} [offset=0]      位移
     * @apiParam {String} [limit=20]      長度
     *
     * @apiSuccess {Number} total 全部獎金
     * @apiSuccess {Number} cnt 筆數
     *
     * @apiSuccessExample {json} 成功:
     *     HTTP/1.1 200 OK
     *      {
     *        "total" : "1",
     *        "cnt" : "5",
     *      }
     *
     * @apiUse MyError
     */

    public function amount() {
      if ( !$obj = Bonus::find('one', array('select' => 'count(*) as c, SUM(remain_price) as total', 'where' => array('user_id = ?', $this->user->id ) )) )
        return Ouput::json('資料庫處理有誤！', 400);

      return Output::json(['total' => $obj->total, 'cnt' => $obj->c] );
    }

    /**
     *
     * @apiIgnore Not finished Method
     *
     * @apiName BonusRecord
     * @api {get} /api/bonus/records 取得獎金交易紀錄
     * @apiGroup Bonus
     * @apiDescription 獎金交易紀錄
     *
     * @apiHeader {string} token 登入後的 Access Token
     * @apiParam {Number} [offset=0]      位移
     * @apiParam {String} [limit=20]      長度
     *
     * @apiSuccess {Number} id 獎金交易ID
     * @apiSuccess {String} type 交易類型（atm, cash）
     * @apiSuccess {String} price 金額
     * @apiSuccess {DateTime} created_at 建立時間
     * @apiSuccess {DateTime} updated_at 更新時間
     *
     * @apiSuccessExample {json} 成功:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "id" : "1",
     *         "type" : "atm",
     *         "price" : "500",
     *         "created_at" : "2018-04-16 12:00:00",
     *         "updated_at" : "2018-04-16 12:00:00",
     *       },
     *         "id" : "2",
     *         "type" : "cash",
     *         "price" : "100",
     *         "created_at" : "2018-04-16 12:00:00",
     *         "updated_at" : "2018-04-16 12:00:00",
     *       },
     *     ]
     *
     * @apiUse MyError
     */

    public function records() {
      $validation = function(&$gets, &$obj) {
        Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
        Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);

        if ( !$obj = BonusReceive::find('all', array('offset' => $gets['offset'], 'limit' => $gets['limit'], 'where' => array('user_id = ?', $this->user->id ) )) )
          return Output::json('查無獎金領取資料！', 400);
      };

      $gets = Input::get();
      if ( $error = Validation::form ($validation, $gets, $obj) )
        return Output::json($error, 400);

      $receives = array_map (function ($receive) {
        $receive = array(
          'id' => $receive->id,
          'type' => $receive->type,
          'price' => $receive->price,
          'created_at' => $receive->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $receive->updated_at->format ('Y-m-d H:i:s'),
        );

        return $receive;
      }, $obj);

      return Output::json($receives);
    }

    /**
     *
     * @apiIgnore Not finished Method
     *
     * @apiName BonusReceive
     * @api {post} /api/bonus/receive 新增兌換獎金
     * @apiGroup Bonus
     * @apiDescription 兌換獎金
     *
     * @apiHeader {string} token 登入後的 Access Token
     *
     * @apiParam {String} type 獎金類型
     * @apiParam {Number} price 金額
     *
     * @apiUse MySuccess
     * @apiUse MyError
     */

    public function receive() {
      $validation = function($posts) {
        Validation::need ($posts, 'type', '付款類型', 0)->isStringOrNumber ()->doTrim ()->length(1, 50);
        Validation::need ($posts, 'price', '金額', 0)->isNumber ()->doTrim ()->greater (0);
      };

      $posts = Input::post();
      if ( $error = Validation::form ($validation, $posts) )
        return Output::json($error, 400);

      if( $this->user->receive($this->user->account, $posts['price'], $posts['type']) )
        return Output::json(['message' => '成功']);

      return Output::json(['message' => '失敗']);
    }

}
