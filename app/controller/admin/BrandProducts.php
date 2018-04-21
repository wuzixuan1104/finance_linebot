<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class BrandProducts extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();

    $this->layout->with ('title', '品牌商品列表')
                 ->with ('current_url', RestfulURL::url ('admin/BrandProducts@index'));
  }

  public function index() {
    $where = Where::create();

    $search = Restful\Search::create ($where)
                            ->input ('ID', function ($val) { return Where::create ('id = ?', $val); }, 'text')
                            ->input ('商品名稱', function ($val) { return Where::create ('name LIKE ?', '%' . $val . '%'); }, 'text')
                            ;

    $total = BrandProduct::count ($where);
    $page  = Pagination::info ($total);
    $objs  = BrandProduct::find ('all', array (
               // 'include' => array ('user'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total);

    return $this->view->setPath('admin/BrandProducts/index.php')

                      ->with ('search', $search)
                      ->with ('pagination', implode ('', $page['links']));
  }

  public function add() {
    return $this->view->setPath ('admin/Advs/add.php');
  }

  public function create() {
    $validation = function (&$posts, &$files) {
      Validation::maybe ($posts, 'status', '狀態', IndexHeaderBanner::STATUS_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (IndexHeaderBanner::$statusTexts));

      Validation::need ($files, 'pic', '圖片')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024);
      Validation::need ($posts, 'title', '標題')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ();
      Validation::need ($posts, 'link', '鏈結')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1);
    };

    $transaction = function ($posts, $files, &$obj) {
      return ($obj = IndexHeaderBanner::create ($posts))
           && $obj->putFiles ($files);
    };

    $posts = Input::post ();
    $files = Input::file ();

    $posts['sort'] = IndexHeaderBanner::count ();

    if ($error = Validation::form ($validation, $posts, $files))
      return refresh (RestfulURL::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = IndexHeaderBanner::getTransactionError ($transaction, $posts, $files, $obj))
      return refresh (RestfulURL::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulURL::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function edit($obj) {
  }

  public function update($obj) {
  }

  public function destroy($obj) {
    $transaction = function($obj) {
      if( $details = $obj->details )
        foreach( $details as $detail )
          if( !$detail->destroy() )
            return false;

      return $obj->destroy();
    };

    if ( $error = Adv::getTransactionError ($transaction, $obj) )
      return refresh (RestfulURL::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulURL::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function show($obj) {
    return $this->view->setPath('admin/BrandProducts/show.php');
  }

  public function enable ($obj) {
    $validation = function (&$posts) {
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

  public function review ($obj) {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'review', '審核', Adv::REVIEW_PASS)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Adv::$reviewTexts));
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
        'review' => $obj->review
      ));
  }
}
