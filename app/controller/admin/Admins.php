<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Admins extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();
    $this->layout->with ('title', '後台會員')
                 ->with ('current_url', RestfulURL::url ('admin/Admins@index'));
  }

  public function index() {
    $where = Where::create ();
    $search = Restful\Search::create ($where)
                            ->input ('名稱', function ($val) { return Where::create ('name LIKE ?', '%' . $val . '%'); }, 'text')
                            ->input ('帳號', function ($val) { return Where::create ('account LIKE ?',  '%' . $val . '%'); }, 'text');

    $total = Admin::count ($where);
    $page  = Pagination::info ($total);
    $objs  = Admin::find ('all', array (
               'order' => Restful\Order::desc ('created_at'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total)
           ->setAddUrl (RestfulURL::add ());
           // ->setSortUrl (RestfulURL::sorts ());

    return $this->view->setPath('admin/Admins/index.php')

                      ->with ('search', $search)
                      ->with ('pagination', implode ('', $page['links']));

  }

  public function add() {
    return $this->view->setPath ('admin/Admins/add.php');
  }

  public function create() {
    $validation = function (&$posts) {
      Validation::need ($posts, 'account', '帳號')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 255);
      Validation::need ($posts, 'password', '密碼')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 255);
      Validation::need ($posts, 'password_check', '確認密碼')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 255);

      if ( $posts['password'] != $posts['password_check'] )
        Validation::error ('密碼與確認密碼不相符！');

      if ($admin = Admin::find ('one', array ('select' => 'id, account, password, token', 'where' => array ('account = ?', $posts['account']))))
        Validation::error ('帳號已存在！');

      $posts['password'] = password_hash ($posts['password'], PASSWORD_DEFAULT);

      Validation::maybe ($posts, 'roles', '角色權限', array ())->isArray ()->doArrayFilter (function ($t, $keys) { return in_array ($t, $keys); }, array_keys (AdminRole::$roleTexts));
    };

    $transaction = function ($posts, &$obj) {
      if (!$obj = Admin::create ($posts))
        return false;


      foreach ($posts['roles'] as $role)
        if (!AdminRole::create (array ('admin_id' => $obj->id, 'role' => $role)))
          return false;

      return true;
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return refresh (RestfulURL::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Admin::getTransactionError ($transaction, $posts, $obj))
      return refresh (RestfulURL::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulURL::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function edit($obj) {
    return $this->view->setPath ('admin/Admins/edit.php');
  }

  public function update($obj) {
    $validation = function (&$posts, $obj) {
      Validation::need ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 255);
      Validation::need ($posts, 'account', '帳號')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 255);

      if ( $posts['password'] != '' ) {
        Validation::need ($posts, 'password', '密碼')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 255);
        Validation::need ($posts, 'password_check', '確認密碼')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 255);

        if ( $posts['password'] != $posts['password_check'] )
          Validation::error ('密碼與確認密碼不相符！');

        if ( !$posts['password'] = password_hash ($posts['password'], PASSWORD_DEFAULT))
          return false;

      } else {
        unset($posts['password']);
      }

      if( $findOtherAccount = Admin::find( 'all', array('where' => Where::create('id != ? AND account = ?' , $obj->id, $posts['account'] ) ) ) )
        Validation::error ('帳號已有人使用！');

      Validation::maybe ($posts, 'roles', '角色權限', array ())->isArray ()->doArrayFilter (function ($t, $keys) { return in_array ($t, $keys); }, array_keys (AdminRole::$roleTexts));
    };

    $transaction = function ($posts, &$obj) {
      if (!($obj->columnsUpdate ($posts) && $obj->save ()))
        return false;

      foreach ($obj->roles as $role)
        if (!$role->destroy ())
          return false;

      foreach ($posts['roles'] as $role)
        if (!AdminRole::create (array ('admin_id' => $obj->id, 'role' => $role)))
          return false;

      return true;
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts, $obj))
      return refresh (RestfulURL::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Admin::getTransactionError ($transaction, $posts, $obj))
      return refresh (RestfulURL::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulURL::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function destroy ($obj) {
    if ($error = Admin::getTransactionError (function ($obj) { return $obj->destroy (); }, $obj))
      return refresh (RestfulURL::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulURL::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function show($obj) {

  }

}
