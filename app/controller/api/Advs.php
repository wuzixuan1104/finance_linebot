<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Advs extends ApiLoginController {

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
   * @apiName Adv
   * @/apiapi {get} /adv 取得廣告
   * @apiGroup Adv
   * @apiDescription 廣告內容
   *
   * @apiHeader {string} token 登入後的 Access Token
   * @apiParam {Number} id  廣告ID
   *
   * @apiSuccess {Number} id   廣告ID
   * @apiSuccess {Object} owner 擁有者資訊
   * @apiSuccess {Number} owner.id 擁有者ID
   * @apiSuccess {String} owner.name 擁有者姓名
   * @apiSuccess {String} owner.pic 擁有者圖片
   * @apiSuccess {Object} details 廣告細項
   * @apiSuccess {Number} details.id 廣告細項ID
   * @apiSuccess {String} details.type 廣告細項類型(picture, youtube, video)
   * @apiSuccess {String} details.pic 廣告細項圖片
   * @apiSuccess {String} details.link 廣告細項連結
   * @apiSuccess {String} details.file 廣告細項檔案
   * @apiSuccess {String} title 標題
   * @apiSuccess {String} description 描述
   * @apiSuccess {String} content 內容
   * @apiSuccess {Boolean} is_like 是否喜歡
   * @apiSuccess {Number} cnt_like 喜歡數
   * @apiSuccess {Number} cnt_message 留言數
   * @apiSuccess {Number} cnt_view 瀏覽次數
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     {
   *         {
   *            "id" : "103",
   *            "owner" : {
   *                "id" : "234",
   *                "name" : "OA",
   *                "pic" : "xxxxxxxxxxx.png",
   *            },
   *            "title" : "影片標題",
   *            "description" : "描述",
   *            "content" : "內容",
   *            "is_like" : true,
   *            "cnt": {
   *                 "like": 8,
   *                 "message": 3,
   *                 "view": 10
   *            },
   *            "created_at" : "2018-04-16 12:00:00",
   *            "updated_at" : "2018-04-16 12:00:00",
   *            "details": [
   *                    {
   *                        "id" : "1",
   *                        "type" : "picture",
   *                        "pic" : "xxxxxxxxxxxx.png",
   *                        "file" : "",
   *                        "link" : "",
   *                    },
   *                    {
   *                        "id" : "2",
   *                        "type" : "video",
   *                        "pic" : "",
   *                        "file" : "xxxxxxx",
   *                        "link" : "",
   *                    },
   *            ]
   *
   *          },
   *     }
   *
   * @apiUse MyError
   */

  public function adv () {
    $validation = function(&$gets, &$adv) {
      Validation::need ($gets, 'id', '廣告ID')->isNumber ()->doTrim ()->greater (0);

      if (!$adv = Adv::find ('one', array ('where' => array('id = ?', $gets['id']) ) ) )
        Validation::error('查無廣告資訊');

      if (!$adv->brand)
        Validation::error('查無品牌資訊');

      if(!$adv->user)
        Validation::error('查無使用者資訊');
    };

    $gets = Input::get ();

    if( $error = Validation::form($validation, $gets, $adv) )
      return Output::json($error, 400);

    $return = array (
        'id' => $adv->id,
        'owner' => $adv->brand_product_id == 0 ? array (
            'id' => $adv->brand->id,
            'name' =>  $adv->brand->name,
            'pic' => $adv->brand->pic->url(),
          ) : array(
            'id' => $adv->user->id,
            'name' => $adv->user->name,
            'pic' => $adv->user->avatar->url(),
          ),
        'title' => $adv->title,
        'description' => $adv->description,
        'content' => $adv->content,
        'is_like' => $this->user->hasLike($adv),
        'cnt' => array (
            'like' => $adv->cnt_like,
            'message' => $adv->cnt_message,
            'view' => $adv->cnt_view,
          ),
        'created_at' => $adv->created_at->format ('Y-m-d H:i:s'),
        'updated_at' => $adv->updated_at->format ('Y-m-d H:i:s'),
        'details' => array_map( function($detail) {
            $advDetail = array(
                    'id' => $detail->id,
                    'type' => $detail->type,
                    'pic' => $detail->pic->url(),
                    'link' => $detail->link,
                    'file' => $detail->file->url(),
                  );
            return $advDetail;
          }, $adv->details),
    );

    return Output::json($return);
  }


  /**
   * @apiName Advs
   * @api {get} /api/advs 取得廣告列表
   * @apiGroup Adv
   * @apiDescription 廣告內容
   *
   * @apiHeader {string}                  token 登入後的 Access Token
   *
   * @apiParam {Number} [offset=0]        位移
   * @apiParam {String} [limit=20]        長度
   *
   * @apiSuccess {Number} id              廣告ID
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

  public function index () {
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
              'pic' => $adv->product->d4 ? $adv->product->d4->pic->url () : ''
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
    }, Adv::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'], 'order' => 'id DESC')));

    $advs = array_values( array_filter($advs) );
    return Output::json($advs);
  }

  /**
   * @apiName CreateAdv
   * @api {post} /api/adv 新增廣告
   * @apiGroup Adv
   * @apiDescription 新增廣告
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} user_id               使用者ID
   * @apiParam {Number} brand_id              品牌ID (註：若為品牌主自行投稿需填入)
   * @apiParam {Number} [brand_product_id]    品牌商品ID (註：若為品牌商品投稿需填入)
   *
   * @apiParam {String} title                 標題
   * @apiParam {String} description           敘述
   * @apiParam {String} content               內容
   *
   * @apiParam {File}  [files]                檔案
   * @apiParam {File}  [pics]                 圖片
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function create() {
    $validation = function (&$posts, $files, &$brand, &$product) {
      Validation::need ($posts, 'brand_id', '品牌ID')->isNumber ()->doTrim ();
      Validation::maybe ($posts, 'brand_product_id', '品牌商品ID', 0)->isNumber ()->doTrim ();
      
      Validation::need ($posts, 'title', '標題')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'description', '敘述')->isStringOrNumber ()->doTrim ();
      Validation::need ($posts, 'content', '內容')->isStringOrNumber ()->doTrim ();

      Validation::maybe ($files, 'pics', '圖片', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024);
      Validation::maybe ($files, 'files', '檔案', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('mp4', 'mov')->filterSize (1, 10 * 1024 * 1024);

      if( !$brand = Brand::find_by_id ($posts['brand_id']) )
        Validation::error('查無此品牌');

      if( $posts['brand_product_id'] && !($product = BrandProduct::find_by_id($posts['brand_product_id'])) )
        Validation::error('查無此品牌商品');

      $posts = array_merge( $posts, array(
        'user_id' => $this->user->id,
        'review' => Adv::REVIEW_FAIL,
        'type' => Adv::TYPE_PICTURE,
        'cnt_like' => 0,
        'cnt_view' => 0,
        'cnt_message' => 0,
      ) );

      $details = array();
      if ( $files['pics'] ) {
        foreach ( $files['pics'] as $pic )
          $details[] = array (
            'type' => AdvDetail::TYPE_PICTURE, '_column' => 'pic', '_tmp' => $pic,
          );
      }

      if ( $files['files'] ) {
        $posts['type'] = Adv::TYPE_VIDEO;
        foreach ( $files['files'] as $file )
          $details[] = array (
            'type' => AdvDetail::TYPE_VIDEO, '_column' => 'file', '_tmp' => $file,
          );
      }

      $details || Validation::error('至少選擇一張圖片或影片');
      count($details) <= 5 || Validation::error('影片或圖片最多五項');

      $posts['details'] = $details;
    };

    $transaction = function ($posts) {
      if( !$adv = Adv::create ($posts) )
        return false;

      foreach( $posts['details'] as $detail ) {
        if ( !$detailObj = AdvDetail::create(array_merge($detail, array('adv_id' => $adv->id, 'link' => '', 'pic' => '', 'file' => '') ) ) )
          return false;

        if( !$detailObj->{$detail['_column']}->put($detail['_tmp']) )
          return false;
      }
      
      if (!$notify = Notify::create (array ('user_id' => $adv->brand->user_id, 'brand_id' => $adv->brand_id, 'send_id' => $this->user->id, 'content' => '哈哈哈', 'read' => Notify::READ_NO)))
        return false;

      return true;
    };

    $posts = Input::post();
    $files['pics'] = Input::file ('pics[]');
    $files['files'] = Input::file ('files[]');

    if ($error = Validation::form ($validation, $posts, $files, $brand, $product))
      return Output::json($error, 400);

    if ($error = Adv::getTransactionError ($transaction, $posts))
      return Output::json($error, 400);

    return Output::json(['message' => "成功"], 200);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName AdvUpdate
   * @api {put} /api/adv 更新廣告
   * @apiGroup Adv
   * @apiDescription 更新廣告
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} id    廣告ID
   * @apiParam {Object} [notify]    通知
   * @apiParam {Number} [notify.user_id]    通知接收者ID (註：若為品牌商品投稿需填入)
   * @apiParam {String} [notify.content]    通知內容 (註：若為品牌商品投稿需填入)
   * @apiParam {String} title 標題
   * @apiParam {String} description 敘述
   * @apiParam {String} content  內容
   * @apiParam {String} review  審核(已審核: pass/ 未審核: fail)
   * @apiParam {Array} [files] 檔案
   * @apiParam {String} [files.name] 檔案名稱
   * @apiParam {String} [files.type] 檔案類型
   * @apiParam {String} [files.tmp_name] 檔案暫存類型
   * @apiParam {Number} [files.error] 檔案錯誤
   * @apiParam {Number} [files.size] 檔案大小
   * @apiParam {Array} [pics] 圖片
   * @apiParam {String} [pics.name] 圖片名稱
   * @apiParam {String} [pics.type] 圖片類型
   * @apiParam {String} [pics.tmp_name] 圖片暫存類型
   * @apiParam {Number} [pics.error] 圖片錯誤
   * @apiParam {Number} [pics.size] 圖片大小
   * @apiParam {Array} [ori_pics] 原始剩餘圖片ID
   * @apiParam {Array} [ori_files] 原始剩餘影片ID
   * @apiUse MySuccess
   * @apiUse MyError
   */
  public function update() {
    $validation = function(&$posts, &$files, &$adv) {
      Validation::need ($posts, 'id', '廣告ID')->isNumber ()->doTrim ();
      Validation::maybe ($posts, 'notify', '通知', array())->isArray ();
      Validation::maybe ($posts, 'review', '審核')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'title', '標題')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'description', '敘述')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'content', '內容')->isStringOrNumber ()->doTrim ();
      // Validation::maybe ($files, 'pics', '圖片', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024);
      // Validation::maybe ($files, 'files', '檔案', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('mp4', 'mov')->filterSize (1, 10 * 1024 * 1024);

      if( !$adv = Adv::find_by_id($posts['id']) )
        Validation::error('查無此廣告');

      if( $posts['notify'] && !empty($posts['notify']['content']) && !User::find_by_id($posts['notify']['user_id']) )
        Validation::error('查無通知的接收者');
    };

    $transaction = function($posts, $files, $adv) {
      if( !( $adv->columnsUpdate ($posts) ) )
        return false;

      if( $posts['notify'] && !Notify::create( array_merge( $posts['notify'], array( 'send_id' => $this->user->id ) ) ) )
        return false;

      // if( $files['pic'] && !$adv->pic->put($files['pic']['tmp_name']) )
      //   return false;

      return $adv->save();
    };

    $posts = Input::put(null, Input::PUT_FORM_DATA);
    $files = Input::file();

    if ($error = Validation::form ($validation, $posts, $files, $adv))
      return Output::json($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $files, $adv))
      return Output::json($error, 400);

    return Output::json(['message' => '成功'], 200);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName CreateMessages
   * @api {post} /api/adv/msg 新增廣告留言
   * @apiGroup Adv
   * @apiDescription 留言
   *
   * @apiHeader {string} token 登入後的 Access Token
   * @apiParam {Number} adv_id    廣告ID
   * @apiParam {String} content   內容
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function createMsg () {
    $validation = function (&$posts, &$adv) {
      Validation::need ($posts, 'adv_id', '廣告ID')->isNumber ()->doTrim ();
      Validation::need ($posts, 'content', '內容')->isStringOrNumber ()->doTrim ();

      if( !$adv = Adv::find_by_id($posts['adv_id']) )
        Validation::error('查無此廣告');

      $posts['user_id'] = $this->user->id;
    };

    $transaction = function ($posts, $adv) {
      if( !$obj = AdvMessage::create ($posts) )
        return false;

      $adv->columnsUpdate( array(
        'cnt_message' => $adv->cnt_message + 1,
      ) );
      return $adv->save();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts, $adv))
      return Output::json($error, 400);

    if ($error = AdvMessage::getTransactionError ($transaction, $posts, $adv))
      return Output::json($error, 400);

    return Output::json(['message' => "成功"], 200);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName Messages
   * @api {get} /api/adv/msgs 取得廣告留言列表
   * @apiGroup Adv
   * @apiDescription 留言
   *
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} adv_id    廣告ID
   * @apiParam {Number} [offset=0]      位移
   * @apiParam {String} [limit=20]      長度
   *
   * @apiSuccess {Number} id 留言ID
   * @apiSuccess {Number} user_id 使用者ID
   * @apiSuccess {String} content 內容
   * @apiSuccess {DateTime} created_at 建立時間
   * @apiSuccess {DateTime} updated_at 更新時間
   *
   * @apiSuccessExample {json} 成功:
   *     HTTP/1.1 200 OK
   *     [
   *          {
   *             "id" : "103",
   *             "user_id" : "66",
   *             "content" : "留言內容1",
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *          },
   *          {
   *             "id" : "104",
   *             "user_id" : "67",
   *             "content" : "留言內容2",
   *             "created_at" : "2018-04-16 12:00:00",
   *             "updated_at" : "2018-04-16 12:00:00",
   *          },
   *      ]
   *
   * @apiUse MyError
   */

  public function msgs () {
    $validation = function(&$gets) {
        Validation::need ($gets, 'adv_id', '廣告ID')->isNumber ()->doTrim ()->greater (0);
        Validation::maybe ($gets, 'offset', '位移', 0)->isNumber ()->doTrim ()->greater (0);
        Validation::maybe ($gets, 'limit', '長度', 20)->isNumber ()->doTrim ()->greater (0);
    };
    $gets = Input::get();
    if( $error = Validation::form($validation, $gets) )
      return Output::json($error, 400);

    $msgs = array_map (function ($msg) {
      return array (
          'id' => $msg->id,
          'user_id' => $msg->user_id,
          'content' => $msg->content,
          'created_at' => $msg->created_at->format ('Y-m-d H:i:s'),
          'updated_at' => $msg->updated_at->format ('Y-m-d H:i:s'),
        );
    }, AdvMessage::find ('all', array ('offset' => $gets['offset'], 'limit' => $gets['limit'], 'where' => array( 'adv_id = ?', $gets['adv_id'] ) ) ) );

    return Output::json($msgs);
  }

  /**
   *
   * @apiIgnore Not finished Method
   *
   * @apiName Likes
   * @api {post} /api/adv/like 新增喜歡
   * @apiGroup Adv
   * @apiDescription 喜歡
   * @apiHeader {string} token 登入後的 Access Token
   *
   * @apiParam {Number} adv_id    廣告ID
   *
   * @apiUse MySuccess
   * @apiUse MyError
   */

  public function like () {
    $validation = function (&$posts, &$adv) {
      Validation::need ($posts, 'adv_id', '廣告ID')->isNumber ()->doTrim ();

      if( !$adv = Adv::find_by_id($posts['adv_id']) )
        Validation::error('查無此廣告');
    };

    $transaction = function ($posts, $adv) {
      if ($this->user->hasLike($adv)) {
        if( $like = AdvLike::find('one', Where::create( 'user_id = ? AND adv_id = ?', $this->user->id, $adv->id ) ) )
            if( !$like->destroy() )
              return false;
      } else {
        if( !AdvLike::create ( array_merge( $posts, array('user_id' => $this->user->id) ) ) )
          return false;
      }
      $adv->columnsUpdate( array(
        'cnt_like' => AdvLike::count( Where::create('adv_id = ?', $adv->id) ),
      ) );
      return $adv->save();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts, $adv))
      return Output::json($error, 400);

    if ($error = AdvLike::getTransactionError ($transaction, $posts, $adv))
      return Output::json($error, 400);

    return Output::json(['message' => "成功"], 200);
  }

}
