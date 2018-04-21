<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Advs extends SiteController {

  public function __construct () {
    parent::__construct ();

    $this->layout->with ('mainTitle', '探索廣告');
    $this->asset->addCSS ('/assets/css/site/Advs.css')
                ->addJS ('/assets/js/site/Advs.js');
    $this->view->with ('asset', $this->asset);
  }

  public function show ($id = 0) {
    if (!(($adv = Adv::find_by_id($id)) && ($adv->enable != Adv::ENABLE_OFF) && ($adv->review != Adv::REVIEW_FAIL) && ($adv->delete != Adv::DELETE_YES) && $adv->details))
      return refresh (URL::base ('advs'));

    // 增加 PV
    AdvView::addPV ($adv);

    $advMayLikes = Adv::find( 'all', array(
                      'include' => array( 'adv_type', 'default'),
                      'order' => 'rand()',
                      'limit' => 4,
                      'where' => Where::create( 'adv_type_id = ? AND id != ?', $adv->adv_type_id, $adv->id ) ) );

    Load::sysFunc ('file.php');
    $this->layout->with ('metas', array (
        array ('name' => 'robots', 'content' => 'index,follow'),
        array ('property' => 'og:url', 'content' => $adv->content_page_url ()),
        array ('property' => 'og:title', 'content' => $adv->title),
        array ('property' => 'og:description', 'content' => $adv->description),
        array ('property' => 'og:site_name', 'content' => 'ADPost'),
        array ('property' => 'fb:admins', 'content' => '100000100541088'),
        array ('property' => 'fb:app_id', 'content' => config ('facebook', 'appId')),
        array ('property' => 'og:locale', 'content' => 'zh_TW'),
        array ('property' => 'og:locale:alternate', 'content' => 'en_US'),
        array ('property' => 'og:type', 'content' => 'article'),

        array ('property' => 'article:author', 'content' => config ('facebook', 'url')),
        array ('property' => 'article:publisher', 'content' => config ('facebook', 'url')),

        array ('property' => 'article:modified_time', 'content' => $adv->updated_at->format ('c')),
        array ('property' => 'article:published_time', 'content' => $adv->created_at->format ('c')),

        array ('property' => 'og:image', 'content' => $adv->default_picture (), 'tag' => 'larger', 'alt' => $adv->title . ' - ADPost'),

        array ('property' => 'og:image:type', 'content' => ($t = get_mime_by_extension ($adv->default_picture ('c1200x630'))) ? $t : 'image/png', 'tag' => 'larger'),
        array ('property' => 'og:image:width', 'tag' => 'larger', 'content' => '1200'),
        array ('property' => 'og:image:height', 'tag' => 'larger', 'content' => '630'),
      ));

    return $this->view->setPath ('site/Advs/show.php')
                      ->with ('adv', $adv)
                      ->with ('advMayLikes', $advMayLikes);
  }

  public function api () {
    $validation = function (&$gets) {
      Validation::maybe ($gets, 'q', '關鍵字', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();
      Validation::maybe ($gets, 'ids', '分類', array ())->isArray ()->doArrayMap (function ($t) { return trim ($t); })->doArrayFilter (function ($t) { return $t; });
      Validation::maybe ($gets, 'type', '類型', 'new')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array ('new', 'pv', 'like'));
      Validation::maybe ($gets, 'page', '頁數', 0)->isNumber ()->doTrim ()->doRemoveHtmlTags ();
    };

    $gets = Input::get ();

    if ($error = Validation::form ($validation, $gets))
      return Output::json ($error, 400);

    $where = Where::create ('enable = ? AND review = ? AND `delete` = ?', Adv::ENABLE_ON, Adv::REVIEW_PASS, Adv::DELETE_NO);
    $gets['q'] && $where->and ('title LIKE ?', '%' . $gets['q'] . '%');
    $gets['ids'] && $where->and ('adv_type_id IN (?)', $gets['ids']);
    $order = $gets['type'] == 'pv' ? 'cnt_view DESC' : ($gets['type'] == 'like' ? 'cnt_like DESC' : 'id DESC');

    $limit = 12;
    $offset = $gets['page'] * $limit;
    $total = Adv::count ($where);
    $advs = Adv::find ('all', array ( 'include' => array('default', 'adv_type'), 'limit' => $limit, 'offset' => $offset, 'order' => $order, 'where' => $where));

    $html = array_map (function ($adv) { return View::create ('site/Advs/_unit.php')->with ('adv', $adv)->get (); }, $advs);

    return Output::json (array (
        'total' => $total,
        'advs' => $html
      ));
  }
  public function index () {
    $q = Input::get ('q');
    $ids = Input::get ('ids');

    $q = $q ? $q : '';
    $ids = $ids ? is_array ($ids) ? $ids : array ($ids) : array ();

    if ($ids || $q)
      return refresh (URL::base ('advs/#q=' . urlencode ($q) . '&' . implode ('&', array_map (function ($id) { return 'ids[]=' . $id; }, $ids))));

    return $this->view->setPath ('site/Advs/index.php')
                      ->with ('q', $q)
                      ->with ('ids', $ids);
  }

  public function add () {
    if (!@User::current ())
      return refresh (URL::base ());

    if (User::current ()->type != User::TYPE_AD)
      return refresh (URL::base ('member'));


    $this->layout->with ('mainTitle', '個人資訊');

    $this->asset->addCSS ('/assets/css/site/Members.css');

    return $this->view->setPath ('site/Advs/add.php');
  }
  public function create () {
    if (!@User::current ())
      return refresh (URL::base ());

    if (User::current ()->type != User::TYPE_AD)
      return refresh (URL::base ('member'));

    Load::lib ('YoutubeTool.php', true);

    $validation = function (&$posts, &$files) {
      Validation::maybe ($posts, 'enable', '上架狀態', Adv::ENABLE_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Adv::$enableTexts));
      Validation::need ($posts, 'title', '廣告標題')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->mbLength (1, 255);
      Validation::need ($posts, 'adv_type_id', '廣告分類')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray ( array_orm_column (AdvType::find ('all', array ('select' => 'id', 'where' => array ('enable = ?', AdvType::ENABLE_ON))), 'id'));
      Validation::maybe ($posts, 'description', '產品簡述', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->mbLength (0, 20);
      Validation::maybe ($posts, 'content', '產品內容', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();
      Validation::maybe ($posts, 'website_url', '網址', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();

      Validation::maybe ($files, 'pics', '圖片', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024);
      Validation::maybe ($posts, 'youtubes', 'Youtube', array ())->isArray ()->doArrayMap (function ($t) { $t['link'] = trim ($t['link']); return $t; })->doArrayFilter (function ($t) { return $t['link']; });

      $details = array ();
      foreach ($posts['youtubes'] as $i => $detail)
        $details[$i] = array ('link' => $detail['link'], 'type' => AdvDetail::TYPE_YOUTUBE);

      foreach ($files['pics'] as $i => $detail)
        $details[$i] = array ('file' => $detail, 'type' => AdvDetail::TYPE_PICTURE, 'link' => '');

      ksort ($details);
      $details || Validation::error ('至少選擇一項影片或圖片。');
      count ($details) <= 5 || Validation::error ('影片或圖片最多五項。');

      $posts['details'] = $details;
      $posts['review'] = Adv::REVIEW_PASS;
    };

    $transaction = function ($posts, &$obj) {
      if (!$obj = Adv::create ($posts))
        return false;

      foreach ($posts['details'] as $detail) {
        if (!$tmp = AdvDetail::create (array_merge ($detail, array ('adv_id' => $obj->id))))
          return false;

        if (isset ($detail['file']) && !$tmp->pic->put ($detail['file']))
          return false;

        if (!isset ($detail['file']) && !$tmp->pic->putUrl (YoutubeTool::biggerYoutubeImageUrl ($tmp->youtubeKey ())))
          return false;
      }

      $isPic = true;
      foreach ($obj->details as $detail)
        $isPic &= $detail->type == AdvDetail::TYPE_PICTURE;

      $obj->type = $isPic ? AdvDetail::TYPE_PICTURE : AdvDetail::TYPE_YOUTUBE;
      return $obj->save ();
    };

    $posts = Input::post ();
    $files['pics'] = Input::file ('pics[]');
    $posts['user_id'] = User::current ()->id;
    $posts['sort'] = Adv::count ();

    if ($error = Validation::form ($validation, $posts, $files))
      return refresh (Url::base ('advs/add'), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Admin::getTransactionError ($transaction, $posts, $obj))
      return refresh (Url::base ('advs/add'), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (Url::base ('member/dashboard'), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function edit ($id) {
    if (!@User::current ())
      return refresh (URL::base ());

    if (User::current ()->type != User::TYPE_AD)
      return refresh (URL::base ('member'));

    if (!$obj = Adv::find ('one', array ('where' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))
      return refresh (URL::base ('member'));

    $this->layout->with ('mainTitle', '個人資訊');

    $this->asset->addCSS ('/assets/css/site/Members.css');

    return $this->view->setPath ('site/Advs/edit.php')
                      ->with ('obj', $obj);
  }

  public function update ($id) {
    if (!@User::current ())
      return refresh (URL::base ());

    if (User::current ()->type != User::TYPE_AD)
      return refresh (URL::base ('member'));

    if (!$obj = Adv::find ('one', array ('where' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))
      return refresh (URL::base ('member'));

    Load::lib ('YoutubeTool.php', true);

    $validation = function (&$obj, &$posts, &$files) {
      Validation::maybe ($posts, 'enable', '上架狀態', Adv::ENABLE_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Adv::$enableTexts));
      Validation::need ($posts, 'title', '廣告標題')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->mbLength (1, 255);
      Validation::need ($posts, 'adv_type_id', '廣告分類')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray ( array_orm_column (AdvType::find ('all', array ('select' => 'id', 'where' => array ('enable = ?', AdvType::ENABLE_ON))), 'id'));
      Validation::maybe ($posts, 'description', '產品簡述', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->mbLength (0, 20);
      Validation::maybe ($posts, 'content', '產品內容', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();
      Validation::maybe ($posts, 'website_url', '網址', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();

      Validation::maybe ($files, 'pics', '圖片', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024);
      Validation::maybe ($posts, 'youtubes', 'Youtube', array ())->isArray ()->doArrayMap (function ($t) { $t['link'] = trim ($t['link']); return $t; })->doArrayFilter (function ($t) { return $t['link']; });
      isset($posts['ori_pics']) || $posts['ori_pics'] = array ();

      $c = count ($posts['youtubes']) + count ($posts['ori_pics']) + count ($files['pics']);
      $c || Validation::error ('至少選擇一項影片或圖片。');
      $c <= 5 || Validation::error ('影片或圖片最多五項。');
    };

    $transaction = function ($posts, &$obj, &$files) {
      if (!($obj->columnsUpdate ($posts) && $obj->save ()))
        return false;

      if (($ori_ids = AdvDetail::getArray ('id', array ('where' => $where = Where::create ('adv_id = ? AND type = ?', $obj->id, AdvDetail::TYPE_YOUTUBE)))) && ($del_ids = array_diff ($ori_ids, array_column ($posts['youtubes'], 'id'))))
        foreach (AdvDetail::find ('all', array ('select' => 'id', 'where' => $where->and ('id IN (?)', $del_ids))) as $del)
          if (!$del->destroy ())
            return false;

      if (($ori_ids = AdvDetail::getArray ('id', array ('where' => $where = Where::create ('adv_id = ? AND type = ?', $obj->id, AdvDetail::TYPE_PICTURE)))) && ($del_ids = array_diff ($ori_ids, $posts['ori_pics'])))
        foreach (AdvDetail::find ('all', array ('select' => 'id', 'where' => $where->and ('id IN (?)', $del_ids))) as $del)
          if (!$del->destroy ())
            return false;


      $details = array ();
      foreach (array_filter($posts['youtubes'], function ($t) { return !isset ($t['id']); }) as $i => $detail)
          $details[$i] = array ('link' => $detail['link'], 'type' => AdvDetail::TYPE_YOUTUBE);

      foreach ($files['pics'] as $i => $detail)
        $details[$i] = array ('file' => $detail, 'type' => AdvDetail::TYPE_PICTURE, 'link' => '');

      ksort ($details);


      foreach ($details as $detail) {
        if (!$tmp = AdvDetail::create (array_merge ($detail, array ('adv_id' => $obj->id))))
          return false;

        if (isset ($detail['file']) && !$tmp->pic->put ($detail['file']))
          return false;

        if (!isset ($detail['file']) && !$tmp->pic->putUrl (YoutubeTool::biggerYoutubeImageUrl ($tmp->youtubeKey ())))
          return false;
      }
      $isPic = true;
      foreach ($obj->details as $detail)
        $isPic &= $detail->type == AdvDetail::TYPE_PICTURE;

      $obj->type = $isPic ? AdvDetail::TYPE_PICTURE : AdvDetail::TYPE_YOUTUBE;

      return $obj->save ();
    };

    $posts = Input::post ();
    $files['pics'] = Input::file ('pics[]');
    $posts['user_id'] = User::current ()->id;

    if ($error = Validation::form ($validation, $obj, $posts, $files))
      return refresh (Url::base ('advs/' . $obj->id . '/edit'), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Admin::getTransactionError ($transaction, $posts, $obj, $files))
      return refresh (Url::base ('advs/' . $obj->id . '/edit'), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (Url::base ('member/dashboard'), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function createPreview () {
    if (!@User::current ())
      return refresh (URL::base ());

    if (User::current ()->type != User::TYPE_AD)
      return refresh (URL::base ('member'));

    Load::lib ('YoutubeTool.php', true);

    $validation = function (&$posts, &$files) {
      Validation::maybe ($posts, 'enable', '上架狀態', Adv::ENABLE_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Adv::$enableTexts));
      Validation::need ($posts, 'title', '廣告標題')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->mbLength (1, 255);
      Validation::need ($posts, 'adv_type_id', '廣告分類')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray ( array_orm_column (AdvType::find ('all', array ('select' => 'id', 'where' => array ('enable = ?', AdvType::ENABLE_ON))), 'id'));
      Validation::maybe ($posts, 'description', '產品簡述', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->mbLength (0, 20);
      Validation::maybe ($posts, 'content', '產品內容', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();
      Validation::maybe ($posts, 'website_url', '網址', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();

      Validation::maybe ($posts, 'details', 'Youtube', array ())->isArray ()->doArrayMap (function ($t) { return trim ($t); })->doArrayFilter (function ($t) { return $t; });
      Validation::maybe ($files, 'details', '圖片', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024);

      $details = array ();
      foreach ($posts['details'] as $i => $detail)
        $details[$i] = array ('link' => $detail, 'type' => AdvDetail::TYPE_YOUTUBE);

      foreach ($files['details'] as $i => $detail)
        $details[$i] = array ('file' => $detail, 'type' => AdvDetail::TYPE_PICTURE, 'link' => '');

      ksort ($details);
      $details || Validation::error ('至少選擇一項影片或圖片。');
      count ($details) <= 5 || Validation::error ('影片或圖片最多五項。');

      $posts['details'] = $details;
      $posts['review'] = Adv::REVIEW_PASS;
    };

    $transaction = function (&$posts, &$obj) {
      $details = array ();
      foreach ($posts['details'] as $detail) {
        $vid = '';

        if (!$tmp = TmpFile::create (array ('file' => '')))
          continue;

        if (isset ($detail['file']) && !$tmp->file->put ($detail['file']))
          continue;

        if (!isset ($detail['file']) && !$tmp->file->putUrl (YoutubeTool::biggerYoutubeImageUrl ($vid = youtube_key ($detail['link']))))
          continue;

        array_push ($details, array ('pic' => $tmp->file->url (), 'vid' => $vid));
      }

      $posts['details'] = $details;

      return true;
    };

    $posts = Input::post ();
    $files['details'] = Input::file ('details[]');
    $posts['user_id'] = User::current ()->id;

    if ($error = Validation::form ($validation, $posts, $files))
      return refresh (Url::base (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Adv::getTransactionError ($transaction, $posts, $obj))
      return refresh (Url::base (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    $type = AdvType::find_by_id ($posts['adv_type_id']);
    $advMayLikes = Adv::find( 'all', array(
                      'order' => 'rand()',
                      'limit' => 4,
                      'where' => Where::create( 'adv_type_id = ?', $type->id) ) );


    return $this->view->setPath ('site/Advs/preview.php')
                      ->with ('type', $type)
                      ->with ('posts', $posts)
                      ->with ('advMayLikes', $advMayLikes);
  }

  public function updatePreview ($id) {
    if (!@User::current ())
      return refresh (URL::base ());

    if (User::current ()->type != User::TYPE_AD)
      return refresh (URL::base ('member'));

    if (!$obj = Adv::find ('one', array ('where' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))
      return refresh (URL::base ('member'));

    Load::lib ('YoutubeTool.php', true);

    $validation = function (&$obj, &$posts, &$files) {
      Validation::maybe ($posts, 'enable', '上架狀態', Adv::ENABLE_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Adv::$enableTexts));
      Validation::need ($posts, 'title', '廣告標題')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->mbLength (1, 255);
      Validation::need ($posts, 'adv_type_id', '廣告分類')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray ( array_orm_column (AdvType::find ('all', array ('select' => 'id', 'where' => array ('enable = ?', AdvType::ENABLE_ON))), 'id'));
      Validation::maybe ($posts, 'description', '產品簡述', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->mbLength (0, 20);
      Validation::maybe ($posts, 'content', '產品內容', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();
      Validation::maybe ($posts, 'website_url', '網址', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();

      Validation::maybe ($posts, 'details', 'Youtube', array ())->isArray ()->doArrayMap (function ($t) { return trim ($t); })->doArrayFilter (function ($t) { return $t; });
      Validation::maybe ($files, 'details', '圖片', array ())->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024);

      $details = array ();
      foreach ($posts['details'] as $i => $detail)
        $details[$i] = array ('link' => $detail, 'type' => AdvDetail::TYPE_YOUTUBE);

      foreach ($files['details'] as $i => $detail)
        $details[$i] = array ('file' => $detail, 'type' => AdvDetail::TYPE_PICTURE, 'link' => '');

      if (empty ($posts['ori_ids'])) {
        ksort ($details);
        $details || Validation::error ('至少選擇一項影片或圖片。');
        count ($details) <= 5 || Validation::error ('影片或圖片最多五項。');
      }

      $posts['details'] = $details;
    };

    $transaction = function (&$posts, &$obj) {
      $details = AdvDetail::find ('all', array ('where' => array ('adv_id = ? AND type = ?', $obj->id, AdvDetail::TYPE_PICTURE)));
      $del_ids = array_diff (array_orm_column ($details, 'id'), $posts['ori_ids']);

      $pics = AdvDetail::find ('all', array ('where' => array ('id NOT IN (?) AND adv_id = ? AND type = ?', $del_ids ? $del_ids : array (0), $obj->id, AdvDetail::TYPE_PICTURE)));
      $details = array ();
      foreach ($pics as $pic)
        array_push ($details, array ('pic' => $pic->pic->url (), 'vid' => ''));

      foreach ($posts['details'] as $detail) {
        $vid = '';

        if (!$tmp = TmpFile::create (array ('file' => '')))
          continue;

        if (isset ($detail['file']) && !$tmp->file->put ($detail['file']))
          continue;

        if (!isset ($detail['file']) && !$tmp->file->putUrl (YoutubeTool::biggerYoutubeImageUrl ($vid = youtube_key ($detail['link']))))
          continue;

        array_push ($details, array ('pic' => $tmp->file->url (), 'vid' => $vid));
      }

      $posts['details'] = $details;

      return true;
    };

    $posts = Input::post ();
    $files['details'] = Input::file ('details[]');
    $posts['user_id'] = User::current ()->id;

    if ($error = Validation::form ($validation, $obj, $posts, $files))
      return refresh (Url::base (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Adv::getTransactionError ($transaction, $posts, $obj))
      return refresh (Url::base (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    $type = AdvType::find_by_id ($posts['adv_type_id']);
    $advMayLikes = Adv::find( 'all', array(
                      'order' => 'rand()',
                      'limit' => 4,
                      'where' => Where::create( 'adv_type_id = ?', $type->id) ) );

    return $this->view->setPath ('site/Advs/preview.php')
                      ->with ('type', $type)
                      ->with ('posts', $posts)
                      ->with ('advMayLikes', $advMayLikes);
  }

  public function enable ($id) {
    if (!@User::current ())
      return Output::json ($error, 400);

    if (User::current ()->type != User::TYPE_AD)
      return Output::json ($error, 400);

    if (!$obj = Adv::find ('one', array ('where' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))
      return Output::json ($error, 400);

    $validation = function (&$posts) {
      Validation::maybe ($posts, 'enable', '開關', Adv::ENABLE_ON)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Adv::$enableTexts));
    };

    $transaction = function ($posts, $obj) {
      return $obj->columnsUpdate ($posts)
          && $obj->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    if ($error = Adv::getTransactionError ($transaction, $posts, $obj))
      return Output::json ($error, 400);

    return Output::json (array (
        'enable' => $obj->enable
      ));
  }

  public function message ($id) {
    if ( !$advObj = Adv::find_by_id($id) )
      return refresh (URL::base ('advs/' . $id));

    if (!$user = @User::current ())
      return refresh (URL::base ('advs/' . $id));

    $validation = function (&$posts, $user, $advObj) {
      Validation::need ($posts, 'content', '內容')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();
      $posts['user_id'] = $user->id;
      $posts['adv_id'] = $advObj->id;
    };

    $transaction = function ($posts, $advObj) {
      if ( !$obj = AdvMessage::create ($posts) )
        return false;

      $advObj->cnt_message = $advObj->cnt_message + 1;
      return $advObj->save();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts, $user, $advObj))
      return refresh (URL::base ('advs/' . $id . '#message'), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = AdvMessage::getTransactionError ($transaction, $posts, $advObj))
      return refresh (URL::base ('advs/' . $id . '#message'), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (URL::base ('advs/' . $id . '#message'), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function like($id) {
    if (!$user = @User::current ())
      return;

    if ( !$advObj = Adv::find_by_id($id) )
      return Output::json ('查無廣告', 400);

    $oriLikes = AdvLike::find( 'all', array( 'where' => Where::create('user_id = ? AND adv_id = ?', $user->id, $id) ) );
    if ( !empty($oriLikes) ) {
      foreach ( $oriLikes as $oriLike ) {
        if ($oriLike->destroy() ) {
          $advObj->cnt_like = $advObj->cnt_like - 1;
          $advObj->save();
        }
      }
    } else {
      $param = array(
        'user_id' => $user->id,
        'adv_id' => $advObj->id,
      );
      if ( AdvLike::create($param) ) {
        $advObj->cnt_like = $advObj->cnt_like + 1;
        $advObj->save();
      }
    }

    return Output::json( array(
      'likeCnt' => $advObj->cnt_like,
      'hasClick' => (bool)AdvLike::count (array ('user_id' => $user->id,'adv_id' => $advObj->id))
    ) );

  }
}
