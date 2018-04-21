<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Users extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();
    $this->layout->with ('title', '前台會員')
                 ->with ('current_url', RestfulURL::url ('admin/Users@index'));
  }

  public function index() {
    $where = Where::create ();
    $search = Restful\Search::create ($where)
                            ->input ('名稱', function ($val) { return Where::create ('name LIKE ?', '%' . $val . '%'); }, 'text')
                            ->input ('信箱', function ($val) { return Where::create ('email LIKE ?',  '%' . $val . '%'); }, 'text')
                            ->input ('電話', function ($val) { return Where::create ('phone LIKE ?',  '%' . $val . '%'); }, 'text')
                          ;

    $total = User::count ($where);
    $page  = Pagination::info ($total);
    $objs  = User::find ('all', array (
               'order' => Restful\Order::desc ('created_at'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total);
           // ->setAddUrl (RestfulURL::add ());
           // ->setSortUrl (RestfulURL::sorts ());

    return $this->view->setPath('admin/Users/index.php')
                      ->with ('search', $search)
                      ->with ('pagination', implode ('', $page['links']));

  }

  public function add() {

  }

  public function create() {

  }

  public function edit($obj) {

  }

  public function update($obj) {

  }

  public function destroy($obj) {
    if ($error = User::getTransactionError (function ($obj) { return $obj->destroy (); }, $obj))
      return refresh (RestfulURL::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulURL::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function show($obj) {
    return $this->view->setPath('admin/Users/show.php');
  }

  public function enable ($obj) {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'enable', '開關', User::ENABLE_ON)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (User::$enableTexts));
    };

    $transaction = function ($posts, $obj) {
      return $obj->columnsUpdate ($posts)
          && $obj->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $obj))
      return Output::json ($error, 400);

    return Output::json (array (
        'enable' => $obj->enable
      ));
  }
  public function active ($obj) {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'active', '驗證', User::ACTIVE_ON)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (User::$activeTexts));
    };

    $transaction = function ($posts, $obj) {
      return $obj->columnsUpdate ($posts)
          && $obj->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    if ($error = User::getTransactionError ($transaction, $posts, $obj))
      return Output::json ($error, 400);

    return Output::json (array (
        'active' => $obj->active
      ));
  }
}
