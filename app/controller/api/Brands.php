<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Brands extends ApiController {

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
   * @apiName Brands
   * @api {get} /brands 取得品牌列表
   * @apiGroup Brand
   * @apiDescription 品牌列表
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} user_id      使用者ID
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id 品牌ID
   * @apiSuccess {Number} user_id 使用者ID
   * @apiSuccess {String} name 品牌名稱
   * @apiSuccess {String} tax_number 統一編號
   * @apiSuccess {String} email 信箱
   * @apiSuccess {String} phone 電話
   * @apiSuccess {String} company_name 公司名稱
   * @apiSuccess {String} company_city 公司城市
   * @apiSuccess {String} company_area 公司區域
   * @apiSuccess {String} company_address 公司地址
   * @apiSuccess {String} website 網站
   * @apiSuccess {String} description 簡述
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *       [
   *          {
   *             "id" : "103",
   *             "user_id" : "66",
   *             "name" : "馬勒艾迪",
   *             "tax_number" : "56555555",
   *             "email" : "cherry@adpost.com.tw",
   *             "phone" : "0900000000",
   *             "company_name" : "Mother AD",
   *             "company_city" : "xxxx",
   *             "company_area" : "xxxx",
   *             "company_address" : "xxxxxxxxx",
   *             "website" : "http//xxxx/xxx",
   *             "description" : "敘述",
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *          },
   *          {
   *             "id" : "104",
   *             "user_id" : "66",
   *             "name" : "馬勒艾迪嘀嘀嘀嘀嘀",
   *             "tax_number" : "56555555",
   *             "email" : "cherry@adpost.com.tw",
   *             "phone" : "0900000000",
   *             "company_name" : "Mother AD",
   *             "company_city" : "xxxx",
   *             "company_area" : "xxxx",
   *             "company_address" : "xxxxxxxxx",
   *             "website" : "http//xxxx/xxx",
   *             "description" : "敘述",
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *          },
   *       ]
   *     }
   *
   * @apiUse MyError
   */

  public function brands() {
  }

  /**
   * @apiName Brand
   * @api {get} /brand 取得品牌
   * @apiGroup Brand
   * @apiDescription 品牌
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} user_id      使用者ID
   * @apiParam {Number} id 品牌ID
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id 品牌ID
   * @apiSuccess {Number} user_id 使用者ID
   * @apiSuccess {String} name 品牌名稱
   * @apiSuccess {String} tax_number 統一編號
   * @apiSuccess {String} email 信箱
   * @apiSuccess {String} phone 電話
   * @apiSuccess {String} company_name 公司名稱
   * @apiSuccess {String} company_city 公司城市
   * @apiSuccess {String} company_area 公司區域
   * @apiSuccess {String} company_address 公司地址
   * @apiSuccess {String} website 網站
   * @apiSuccess {String} description 簡述
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *             "id" : "103",
   *             "user_id" : "66",
   *             "name" : "馬勒艾迪",
   *             "tax_number" : "56555555",
   *             "email" : "cherry@adpost.com.tw",
   *             "phone" : "0900000000",
   *             "company_name" : "Mother AD",
   *             "company_city" : "xxxx",
   *             "company_area" : "xxxx",
   *             "company_address" : "xxxxxxxxx",
   *             "website" : "http//xxxx/xxx",
   *             "description" : "敘述",
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *     }
   *
   * @apiUse MyError
   */

  public function brand() {
  }

  /**
   * @apiName CreateBrand
   * @api {post} /brand 新增品牌
   * @apiGroup Brand
   * @apiDescription 新增品牌
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

  }

  /**
   * @apiName Products
   * @api {get} /brand/products 取得品牌商品列表
   * @apiGroup Brand
   * @apiDescription 商品
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {String} [review]        審核狀態 (已審核: yes, 未審核: no)
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id 品牌商品ID
   * @apiSuccess {Number} brand_id 品牌ID
   * @apiSuccess {String} brand_name 品牌名稱
   * @apiSuccess {String} name 商品名稱
   * @apiSuccess {String} description 內容
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *       [
   *          {
   *             "id" : "103",
   *             "brand_id" : "66",
   *             "brand_name" : "品牌名稱",
   *             "name" : "商品名稱",
   *             "description" : "敘述",
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *          },
   *          {
   *             "id" : "104",
   *             "brand_id" : "67",
   *             "brand_name" : "品牌名稱",
   *             "name" : "商品名稱",
   *             "description" : "敘述",
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *          },
   *       ]
   *     }
   *
   * @apiUse MyError
   */

  public function products() {
  }

  /**
   * @apiName Product
   * @api {get} /brand/product 取得品牌商品
   * @apiGroup Brand
   * @apiDescription 品牌商品
   *
   * @apiHeader {string} token 登入後的 Access Token
   * @apiParam {Number} id 品牌商品ID
   *
   * @apiSuccess {Number} id 品牌商品ID
   * @apiSuccess {Object} brand 品牌資訊
   * @apiSuccess {Number} brand.id 品牌ID
   * @apiSuccess {String} brand.name 品牌名稱
   * @apiSuccess {Number} brand.user_id 品牌使用者ID
   * @apiSuccess {String} brand.user_name 品牌使用者
   * @apiSuccess {String} name 商品名稱
   * @apiSuccess {String} rule 拍攝限制
   * @apiSuccess {Number} cnt_shoot 拍攝人數
   * @apiSuccess {String} description 敘述
   * @apiSuccess {Array} details 商品詳細內容
   * @apiSuccess {String} details.type 商品詳細類型 (picture, youtube, video)
   * @apiSuccess {String} [details.url] 連結 (youtube, video)
   * @apiSuccess {String} [details.pic] 圖片 (picture)
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *       [
   *          {
   *             "id" : "103",
   *             "brand" : {
   *                "id" : "10",
   *                "name" : "品牌名稱",
   *                "user_id" : "2",
   *                "user_name" : "cherry",
   *             }
   *             "name" : "商品名稱",
   *             "rule" : "拍攝限制",
   *             "cnt_shoot" : "拍攝人數",
   *             "description" : "敘述",
   *             "details" : [
   *                {
   *                    "type" : "picture",
   *                    "pic" : "xxxxxxx.png",
   *                },
   *                {
   *                    "type" : "youtube",
   *                    "url" : "http://xxxxxxxxxx",
   *                },
   *                {
   *                    "type" : "video",
   *                    "url" : "http://xxxxxxxxx",
   *                }
   *             ],
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *          },
   *       ]
   *     }
   *
   * @apiUse MyError
   */

  public function product() {
  }


}
