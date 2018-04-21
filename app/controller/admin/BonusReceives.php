<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class BonusReceives extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();

    $this->layout->with ('title', '獎金領取列表')
                 ->with ('current_url', RestfulURL::url ('admin/BonusReceives@index'));
  }

  public function index () {
    $where = Where::create();

    $search = Restful\Search::create ($where)
                            ->input ('標題', function ($val) {
                              $advIds = Adv::getArray ('id', array ('where' => Where::create ('title LIKE ?', '%' . $val . '%')));
                              return $where = Where::create ('adv_id IN (?)', $advIds) ? $where : Where::create();
                            }, 'text');

    $total = BonusReceive::count ($where);
    $page  = Pagination::info ($total);
    $objs  = BonusReceive::find ('all', array (
               'include' => array ('user'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total);
           // ->setAddUrl (RestfulURL::add ());

    return $this->view->setPath('admin/BonusReceives/index.php')
                      ->with ('search', $search)
                      ->with ('pagination', implode ('', $page['links']));
  }

  public function create() {

  }

  public function add() {
    return $this->view->setPath ('admin/BonusReceives/add.php');
  }

  public function edit($obj) {

  }

  public function update($obj) {

  }

  public function show($obj) {
  }

  public function destroy ($obj) {
    if ($error = BonusReceive::getTransactionError (function ($obj) { return $obj->destroy (); }, $obj))
      return refresh (RestfulURL::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulURL::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function receive ($obj) {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'is_receive', '領取', BonusReceive::RECEIVE_NO)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (BonusReceive::$receiveTexts));
    };

    $transaction = function ($posts, $obj) {
      return $obj->columnsUpdate ($posts)
          && $obj->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    if ($error = BonusReceive::getTransactionError ($transaction, $posts, $obj))
      return Output::json ($error, 400);

    return Output::json (array (
        'is_receive' => $obj->is_receive
      ));
  }
}
