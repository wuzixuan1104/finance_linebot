<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Brands extends ApiLoginController {

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
   * @apiName Brands
   * @/apiapi {get} /brands 取得品牌列表
   * @apiGroup Brand
   * @apiDescription 品牌列表
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id 品牌ID
   * @apiSuccess {Object} user 使用者
   * @apiSuccess {Number} user.id 使用者ID
   * @apiSuccess {String} user.name 使用者名稱
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
   *       [
   *          {
   *             "id" : "103",
   *             "user" : {
   *                "id" : "1",
   *                "name" : "oa",
   *             },
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
   *             "user" : {
   *                "id" : "1",
   *                "name" : "oa",
   *             },
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

    $brands = array_map (function ($brand) {
      if( !$brand->user)
        return null;

      $brand = array (
          'id' => $brand->id,
          'user' => array(
            'id' => $brand->user->id,
            'name' => $brand->user->name,
          ),
          'name' => $brand->name,
          'tax_number' => $brand->tax_number,
          'email' => $brand->email,
          'phone' => $brand->phone,
          'company_name' => $brand->company_name,
          'company_city' => $brand->company_city,
          'company_area' => $brand->company_area,
          'company_address' => $brand->company_address,
          'website' => $brand->website,
          'description' => $brand->description,
          'created_at' => $brand->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $brand->updated_at->format ('Y-m-d H:i:s'),
        );

      return $brand;
    }, Brand::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'])));

    return Output::json($brands);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName Brand
   * @api {get} /api/brand 取得品牌
   * @apiGroup Brand
   * @apiDescription 品牌
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} id      品牌ID
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
    $validation = function(&$gets, &$brand) {
      Validation::need ($gets, 'id', '品牌ID')->isNumber ()->doTrim ()->greater (0);
      if( !$brand = Brand::find_by_id($gets['id']) )
        Validation::error('查無此資料！');
    };
    $gets = Input::get ();
    if( $error = Validation::form($validation, $gets, $brand) )
      return Output::json($error, 400);

    $return = array (
        'id' => $brand->id,
        'user_id' => $brand->user_id,
        'name' => $brand->name,
        'tax_number' => $brand->tax_number,
        'email' => $brand->email,
        'phone' => $brand->phone,
        'company_name' => $brand->company_name,
        'company_city' => $brand->company_city,
        'company_area' => $brand->company_area,
        'company_address' => $brand->company_address,
        'website' => $brand->website,
        'description' => $brand->description,
        'created_at' => $brand->created_at->format ('Y-m-d H:i:s'),
        'updated_at' => $brand->updated_at->format ('Y-m-d H:i:s'),
      );

    return Output::json($return);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName CreateBrand
   * @api {post} /api/brand 新增品牌
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
    $validation = function (&$posts, &$files) {
      Validation::need ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'tax_number', '統一編號')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'email', '信箱')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'phone', '電話')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'company_name', '公司名稱')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'company_city', '公司城市')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'company_area', '公司區域')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'company_address', '公司地址')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'website', '網站')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'description', '描述')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($files, 'pic', '圖片', array ())->isArray ()->isUploadFile ();
    };

    $transaction = function ($posts, $files) {
      if( !$obj = Brand::create ( array_merge( $posts, array('pic' => '', 'user_id' => $this->user->id) ) ) )
        return false;

      if ($files['pic'] && !( $obj->pic->put($files['pic']) && $obj->save() ) )
        return false;

      return true;
    };

    $posts = Input::post();
    $files = Input::file();

    if ($error = Validation::form ($validation, $posts, $files))
      return Output::json($error, 400);

    if ($error = Brand::getTransactionError ($transaction, $posts, $files))
      return Output::json($error, 400);

    return Output::json(['message' => "成功"], 200);
  }


  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName BrandUpdate
   * @api {put} /api/brand 更新品牌
   * @apiGroup Brand
   * @apiDescription 更新品牌
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} id 品牌ID
   * @apiParam {String} name 名稱
   * @apiParam {String} tax_number 統一編號
   * @apiParam {String} email 信箱
   * @apiParam {String} phone 電話
   * @apiParam {String} company_name 公司名稱
   * @apiParam {String} company_city 公司城市
   * @apiParam {String} company_area 公司區域
   * @apiParam {String} company_address 公司住址
   * @apiParam {String} website 網站
   * @apiParam {String} description  敘述
   * @apiParam {String} pic 圖片
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function update() {
    $validation = function(&$posts, &$files, &$brand) {
      Validation::need  ($posts, 'id', '品牌ID')->isNumber ()->doTrim ()->length(1, 11);
      Validation::maybe ($posts, 'name', '姓名')->isStringOrNumber ()->doTrim ()->length(1, 191);
      Validation::maybe ($posts, 'tax_number', '統一編號')->isStringOrNumber ()->doTrim ()->length(1, 50);
      Validation::maybe ($posts, 'email', '信箱')->isEmail ()->doTrim ()->length(1, 191);
      Validation::maybe ($posts, 'phone', '手機')->isStringOrNumber ()->doTrim ()->length(1, 50);
      Validation::maybe ($posts, 'company_name', '公司名稱')->isStringOrNumber ()->doTrim ()->length(1, 191);
      Validation::maybe ($posts, 'company_city', '公司城市')->isStringOrNumber ()->doTrim ()->length(1, 50);
      Validation::maybe ($posts, 'company_area', '公司區域')->isStringOrNumber ()->doTrim ()->length(1, 50);
      Validation::maybe ($posts, 'company_address', '公司地址')->isStringOrNumber ()->doTrim ()->length(1, 191);
      Validation::maybe ($posts, 'website', '網站')->isStringOrNumber ()->doTrim ()->length(1, 191);
      Validation::maybe ($posts, 'description', '敘述')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($files, 'pic', '圖片', array ())->isArray ()->isUploadFile ();

      if( !$brand = Brand::find_by_id($posts['id']) )
        Validation::error('查無此品牌');
    };

    $transaction = function($posts, $files, $brand) {
      if( !( $brand->columnsUpdate ($posts) ) )
        return false;

      if( $files['pic'] && !$brand->pic->put($files['pic']['tmp_name']) )
        return false;

      return $brand->save();
    };

    $posts = Input::put(null, Input::PUT_FORM_DATA);
    $files = Input::file();

    if ($error = Validation::form ($validation, $posts, $files, $brand))
      return Output::json($error, 400);

    if ($error = Brand::getTransactionError ($transaction, $posts, $files, $brand))
      return Output::json($error, 400);

    return Output::json(['message' => '成功'], 200);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName Products
   * @api {get} /api/brand/products 取得品牌商品列表
   * @apiGroup Brand
   * @apiDescription 商品
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} [brand_id]      品牌ID
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id 品牌商品ID
   * @apiSuccess {Object} brand 品牌
   * @apiSuccess {Number} brand.id 品牌ID
   * @apiSuccess {String} brand.name 品牌名稱
   * @apiSuccess {String} name 商品名稱
   * @apiSuccess {String} description 內容
   * @apiSuccess {String} rule 規則
   * @apiSuccess {String} cnt_shoot 拍攝數
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *       [
   *          {
   *             "id" : "103",
   *             "brand" : {
   *                "id" : "123",
   *                "name" : "123test",
   *             },
   *             "name" : "商品名稱",
   *             "rule" : "規則",
   *             "cnt_shoot" : "拍攝數",
   *             "description" : "敘述",
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *          },
   *       ]
   *
   * @apiUse MyError
   */

  public function products() {
    $validation = function(&$gets, &$products) {
      Validation::maybe ($gets, 'brand_id', '品牌ID', 0)->isNumber ()->doTrim ()->greater (0);
      Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
      Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);

      if( $gets['brand_id'] && !$products = BrandProduct::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'], 'where' => array('brand_id = ?', $gets['brand_id']) )) )
        Validation::error('查無該品牌商品！');
      else
        if( !$products = BrandProduct::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit']) ) )
          Validation::error('查無所有品牌商品！');
    };

    $gets = Input::get ();

    if( $error = Validation::form($validation, $gets, $products) )
      return Output::json($error, 400);

    if( !$products )
      return Output::json('查無品牌產品！', 400);

    $brandProducts = array_map (function ($brandProduct) {
      if( !$brandProduct->brand )
        return null;

      $brandProduct = array (
          'id' => $brandProduct->id,
          'brand' => array(
            'id' => $brandProduct->brand->id,
            'name' => $brandProduct->brand->name,
          ),
          'name' => $brandProduct->name,
          'rule' => $brandProduct->rule,
          'description' => $brandProduct->description,
          'cnt_shoot' => $brandProduct->cnt_shoot,
          'created_at' => $brandProduct->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $brandProduct->updated_at->format ('Y-m-d H:i:s'),
        );

      return $brandProduct;
    }, $products);

    return Output::json($brandProducts);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName Product
   * @api {get} /api/brand/product 取得品牌商品
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
   *                "user" : {
   *                  "id" : "2",
   *                  "name" : "cherry",
   *                }
   *             }
   *             "name" : "商品名稱",
   *             "rule" : "拍攝限制",
   *             "cnt_shoot" : "拍攝人數",
   *             "description" : "敘述",
   *             "details" : [
   *                {
   *                    "type" : "picture",
   *                    "pic" : "xxxxxxx.png",
   *                    "url" : "",
   *                    "file" : "",
   *                },
   *                {
   *                    "type" : "youtube",
   *                    "pic" : "xxxxxxx.png",
   *                    "url" : "http://xxxxxxxxxx",
   *                    "file" : "",
   *                },
   *                {
   *                    "type" : "video",
   *                    "pic" : "",
   *                    "url" : "http://xxxxxxxxx",
   *                    "file" : "",
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
    $validation = function(&$gets, &$product) {
      Validation::need ($gets, 'id', '品牌商品ID')->isNumber ()->doTrim ()->greater (0);

      if (!$product = BrandProduct::find ('one', array ('where' => array('id = ?', $gets['id']) ) ) )
        Validation::error('查無廣告資訊');
    };

    $gets = Input::get ();

    if( $error = Validation::form($validation, $gets, $product) )
      return Output::json($error, 400);

    $return = array (
        'id' => $product->id,
        'brand' => array(
          'id' => $product->brand->id,
          'name' => $product->brand->name,
          'user' => array(
            'id' => $product->brand->user_id,
            'name' => User::find_by_id($product->brand->user_id)->name,
          ),
        ),
        'name' => $product->name,
        'rule' => $product->rule,
        'description' => $product->description,
        'cnt_shoot' => $product->cnt_shoot,
        'created_at' => $product->created_at->format ('Y-m-d H:i:s'),
        'updated_at' => $product->updated_at->format ('Y-m-d H:i:s'),
        'details' => array_map( function($detail) {
            $productDetail = array(
                    'id' => $detail->id,
                    'type' => $detail->type,
                    'pic' => $detail->pic->url(),
                    'url' => $detail->url,
                    'file' => $detail->file->url(),
                  );
            return $productDetail;
          }, $product->details),
    );

    return Output::json($return);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName createProduct
   * @api {post} /api/brand/product 新增品牌商品
   * @apiGroup Brand
   * @apiDescription 新增品牌商品
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} brand_id 品牌ID
   * @apiParam {String} name 名稱
   * @apiParam {String} rule 規則
   * @apiParam {String} description 描述
   * @apiParam {String} cnt_shoot 拍攝次數
   * @apiParam {Array} [pics] 圖片
   * @apiParam {Array} [files] 檔案
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */
  public function createProduct() {
    $validation = function (&$posts, &$files) {
      Validation::need ($posts, 'brand_id', '品牌ID')->isNumber ()->doTrim ()->greater (0);
      Validation::need ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'rule', '統一編號')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'description', '信箱')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'cnt_shoot', '電話')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($files, 'pics', '圖片', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024);
      Validation::maybe ($files, 'files', '檔案', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('mp4', 'mov')->filterSize (1, 10 * 1024 * 1024);

      if( !$brand = Brand::find_by_id($posts['brand_id']) )
        Validation::error('查無此品牌');

      $posts['type'] = BrandProductDetail::TYPE_PICTURE;

      $details = array();
      if ( $files['pics'] ) {
        foreach ( $files['pics'] as $pic )
          $details[] = array (
            'type' => BrandProductDetail::TYPE_PICTURE, '_column' => 'pic', '_tmp' => $pic,
          );
      }

      if ( $files['files'] ) {
        $posts['type'] = BrandProductDetail::TYPE_VIDEO;
        foreach ( $files['files'] as $file )
          $details[] = array (
            'type' => BrandProductDetail::TYPE_VIDEO, '_column' => 'file', '_tmp' => $file,
          );
      }

      $details || Validation::error('至少選擇一張圖片或影片');
      count($details) <= 5 || Validation::error('影片或圖片最多五項');

      $posts['details'] = $details;
    };

    $transaction = function ($posts) {
      if( !$obj = BrandProduct::create ($posts) )
        return false;

      foreach( $posts['details'] as $detail ) {
        if ( !$detailObj = BrandProductDetail::create(array_merge($detail, array(
          'brand_product_id' => $obj->id, 'url' => '', 'pic' => '', 'file' => '') ) ) )
          return false;

        if( !$detailObj->{$detail['_column']}->put($detail['_tmp']) )
          return false;
      }
      return true;
    };

    $posts = Input::post();
    $files['pics'] = Input::file ('pics[]');
    $files['files'] = Input::file ('files[]');

    if ($error = Validation::form ($validation, $posts, $files))
      return Output::json($error, 400);

    if ($error = Brand::getTransactionError ($transaction, $posts))
      return Output::json($error, 400);

    return Output::json(['message' => "成功"], 200);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName updateProduct
   * @api {put} /api/brand/product 更新品牌商品
   * @apiGroup Brand
   * @apiDescription 更新品牌商品
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} id 品牌商品ID
   * @apiParam {String} name 名稱
   * @apiParam {String} rule 規則
   * @apiParam {String} description 描述
   * @apiParam {String} cnt_shoot 拍攝次數
   * @apiParam {Array} [pics] 圖片
   * @apiParam {Array} [files] 檔案
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */
  public function updateProduct() {
    $validation = function (&$posts, &$files, &$product) {
      Validation::need ($posts, 'id', '品牌商品ID')->isNumber ()->doTrim ()->greater (0);
      Validation::maybe ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'rule', '統一編號')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'description', '信箱')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'cnt_shoot', '電話')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($files, 'pics', '圖片', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024);
      Validation::maybe ($files, 'files', '檔案', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('mp4', 'mov')->filterSize (1, 10 * 1024 * 1024);
      Validation::maybe ($posts, 'ori_pics', '更新後保留的舊圖片ID', array ())->isArray ();
      Validation::maybe ($posts, 'ori_files', '更新後保留的舊檔案ID', array ())->isArray ();

      print_r($posts);
      die;
      if( !$product = BrandProduct::find_by_id($posts['id']) )
        Validation::error('查無此品牌商品');

      $delete = array();
      $details = array();

      if ( $files['pics'] ) {
        $oriPics = BrandProductDetail::getArray('id', array('where' => Where::create('brand_product_id = ? AND type = ?', $posts['id'], BrandProductDetail::TYPE_PICTURE ) ) );
        $delete = array_merge( $delete, array_diff ($oriPics, $posts['ori_pics']) );

        foreach ( $files['pics'] as $pic )
          $details[] = array (
            'type' => BrandProductDetail::TYPE_PICTURE, '_column' => 'pic', '_tmp' => $pic,
          );
      }

      if ( $files['files'] ) {
        $orifiles = BrandProductDetail::getArray('id', array('where' => Where::create('brand_product_id = ? AND type = ?', $posts['id'], BrandProductDetail::TYPE_VIDEO ) ) );
        $delete = array_merge( $delete, array_diff ($orifiles, $posts['ori_files']) );

        foreach ( $files['files'] as $file )
          $details[] = array (
            'type' => BrandProductDetail::TYPE_VIDEO, '_column' => 'file', '_tmp' => $file,
          );
      }

      $details || $posts['ori_pics'] || $posts['ori_files'] || Validation::error('至少選擇一張圖片或影片');
      count($details) <= 5 || Validation::error('影片或圖片最多五項');

      $posts['details'] = $details;
      $posts['delete'] = $delete;
    };

    $transaction = function ($posts, $product) {
      if( !$product->updateColumn ($posts) )
        return false;

      if( $posts['delete'] )
        foreach (BrandProductDetail::find ('all', array ('select' => 'id', 'where' => $where->and ('id IN (?)', $posts['delete']))) as $del)
          if (!$del->destroy ())
            return false;

      foreach( $posts['details'] as $detail ) {
        if ( !$detailObj = BrandProductDetail::create( array_merge($detail, array(
          'brand_product_id' => $obj->id, 'url' => '', 'pic' => '', 'file' => '') ) ) )
          return false;

        if( !$detailObj->{$detail['_column']}->put($detail['_tmp'][$detail['_column']]['tmp_name']) )
          return false;
      }
      return true;
    };

    $posts = Input::post();
    $posts['ori_pics'] = Input::put('ori_pics[]', Input::PUT_FORM_DATA);
    var_dump ($posts);
    exit;
    $files['pics'] = Input::file ('pics[]');
    $files['files'] = Input::file ('files[]');

    if ($error = Validation::form ($validation, $posts, $files, $product))
      return Output::json($error, 400);

    if ($error = Brand::getTransactionError ($transaction, $posts, $product))
      return Output::json($error, 400);

    return Output::json(['message' => "成功"], 200);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName BrandAdvs
   * @api {get} /api/brand/advs 取得品牌廣告列表
   * @apiGroup Brand
   * @apiDescription 品牌廣告列表
   *
   * @apiHeader {string}                token 登入後的 Access Token
   *
   * @apiParam {Number} brand_id     品牌ID
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id     廣告ID
   *
   * @apiSuccess {Object} default         預設圖
   * @apiSuccess {Number} default.id      預設圖ID
   * @apiSuccess {String} default.pic     預設圖網址
   *
   * @apiSuccess {Object} user            使用者資訊
   * @apiSuccess {Number} user.id         使用者ID
   * @apiSuccess {String} user.name       使用者姓名
   * @apiSuccess {String} user.pic        使用者圖片
   *
   * @apiSuccess {Object} brand           品牌
   * @apiSuccess {Number} brand.id        品牌ID
   * @apiSuccess {String} brand.name      品牌名稱
   *
   * @apiSuccess {Object} [product]       品牌
   * @apiSuccess {Number} [product.id]    品牌ID
   * @apiSuccess {String} [product.name]  品牌名稱
   * @apiSuccess {String} [product.pic]   品牌預設圖
   *
   * @apiSuccess {String} title           標題
   * @apiSuccess {String} description     描述
   * @apiSuccess {Boolean} is_like 是否喜歡
   * @apiSuccess {Object} cnt             數量
   * @apiSuccess {Number} cnt.like        喜歡數
   * @apiSuccess {Number} cnt.message     留言數
   * @apiSuccess {Number} cnt.view        瀏覽次數
   *
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     [
   *         {
   *             "id": 4,
   *             "default": {
   *                 "id": 13,
   *                 "pic": ""
   *             },
   *             "user": {
   *                 "id": 6,
   *                 "name": "欒擘弁",
   *                 "avatar": ""
   *             },
   *             "brand": {
   *                 "id": 1,
   *                 "name": "脫舷裊倌枚卷焙聊瑯坐谷軒名猾柴果崆貸蝌聖縈枴疋窗暖筐嫗蚊孝賃訥蚓刈兕協棒係否撐什篤枋渴盎經而怨丑牽背荒分敏命赭牘磐氦螞羔癆厭襪壙葛緘慼沛嚕穿補器綢彙皴登抨瘉肫星君客莊犒然紙孜簿蔚檀昭憤淚"
   *             },
   *             "title": "失薛固楚帑瀉召蛭蘑檔",
   *             "description": "用稻展貶扭祗懣軒范窠沅猩滌獰狸蜓感型皇娘觀氣汨蕊獗恪液瞳臚窄朧弦催嵐婆蕩杵譬哪仿軸皖獸帛幫蓉窒瀛窘搶昨妹証境孫神皓礪徹深",
   *            "is_like" : true,
   *             "cnt": {
   *                 "like": 3,
   *                 "message": 6,
   *                 "view": 7
   *             },
   *             "created_at": "2018-04-11 15:06:59",
   *             "updated_at": "2018-04-11 15:06:59"
   *         },
   *         {
   *             "id": 5,
   *             "default": {
   *                 "id": 16,
   *                 "pic": ""
   *             },
   *             "user": {
   *                 "id": 10,
   *                 "name": "勞萍懍慄",
   *                 "avatar": ""
   *             },
   *             "brand": {
   *                 "id": 2,
   *                 "name": "著剃腑孵格請抑衢寧易唬袁叮病詢皎螃彿膾漕涵密漲泌價左帶編盜映戟愎尬濤蜇炙煌諒虐梨訊於楚蕃癸蔗女燃筋秧悔珀住佣榨蜥菁"
   *             },
   *             "product": {
   *                 "id": 1,
   *                 "name": "瑙升瞑哈化努伙政",
   *                 "pic": ""
   *             },
   *             "title": "宰豕嗅極跟",
   *             "description": "紜佛嶺嬰栽圃擦膀晶參競蚵乒戳修繆員澎豢桔躊併拋莉宰櫚袁唬貍副江捷穫擱映褸縊層楊儈凡桅碼滋協請俘木購慇微傲牘踫能胖庠指蔔楔喱矮貊柴讖",
   *             "cnt": {
   *                 "like": 8,
   *                 "message": 3,
   *                 "view": 10
   *             },
   *             "created_at": "2018-04-11 15:06:59",
   *             "updated_at": "2018-04-11 15:06:59"
   *         },
   *     ]
   *
   *
   * @apiUse MyError
   */

  public function advs () {
    $validation = function(&$gets) {
      Validation::need ($gets, 'brand_id', '品牌ID')->isNumber ()->doTrim ()->greater (0);
      Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
      Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);

      if( !Adv::find_by_brand_id($gets['brand_id']))
        Validation::error('查無此品牌的廣告資料！');
    };

    $gets = Input::get ();

    if( $error = Validation::form($validation, $gets) )
      return Output::json($error, 400);

    $advs = array_map (function ($adv) {
      if( !$adv->brand )
        return null;

      if( !$adv->d4 )
        return null;

      if( !$adv->user )
        return null;

      $adv = array (
        'id' => $adv->id,
          'default' => array (
              'id' => $adv->d4->id,
              'pic' => $adv->d4->pic->url ()
            ),
          'user' => array (
              'id' => $adv->user->id,
              'name' => $adv->user->name,
              'avatar' => $adv->user->avatar->url (),
            ),
          'brand' => array (
              'id' => $adv->brand->id,
              'name' => $adv->brand->name,
              'pic' => $adv->brand->pic->url(),
            ),
          'product' => $adv->product ? array (
              'id' => $adv->product->id,
              'name' => $adv->product->name,
              'pic' => $adv->product->d4->pic->url ()
            ) : array (),
          'title' => $adv->title,
          'description' => $adv->description,
          'is_like' => $this->user->hasLike($adv),
          'cnt' => array (
              'like' => $adv->cnt_like,
              'message' => $adv->cnt_message,
              'view' => $adv->cnt_view,
            ),
          'created_at' => $adv->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $adv->updated_at->format ('Y-m-d H:i:s'),
        );

      if (!$adv['product'])
        unset ($adv['product']);

      return $adv;
    }, Adv::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'], 'where' => array('brand_id = ?', $gets['brand_id']))));

    $advs = array_values( array_filter($advs) );
    return Output::json($advs);
  }
}
