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
   *
   * @apiIgnore Not finished Method
   *
   * @apiName User
   * @/apiapi {get} /user 取得個人資訊
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
   *
   * @apiIgnore Not finished Method
   *
   * @apiName Update
   * @api {post} /api/user 編輯個人資料
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

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName UserBrands
   * @api {get} /api/user/brands 取得使用者品牌列表
   * @apiGroup User
   * @apiDescription 使用者品牌列表
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

  public function brands() {
    $validation = function(&$gets) {
      Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
      Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);
    };

    $gets = Input::get ();

    if( $error = Validation::form($validation, $gets) )
      return Output::json($error, 400);

    $brands = array_map (function ($brand) {
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
    }, Brand::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'], 'where' => array('user_id = ?', $this->user->id) )));

    return Output::json($brands);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName UserAdvs
   * @api {get} /api/user/advs 取得使用者廣告
   * @apiGroup User
   * @apiDescription 使用者廣告
   *
   * @apiHeader {string}                token 登入後的 Access Token
   *
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

  public function advs() {
    $validation = function(&$gets) {
      Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
      Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);
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
    }, Adv::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'], 'where' => array('user_id = ?', $this->user->id) )));

    $advs = array_values( array_filter($advs) );
    return Output::json($advs);
  }
}
