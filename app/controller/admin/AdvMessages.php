<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class AdvMessages extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();
    $this->layout->with ('title', '廣告留言')
                 ->with ('current_url', RestfulURL::url ('admin/Advs@index'));
  }

  public function index() {
    $where = Where::create ('adv_id = ?', $this->parent->id);

    $search = Restful\Search::create ($where)
                            ->input ('留言內容', function ($val) { return Where::create ('content LIKE ?', '%' . $val . '%'); }, 'text')
                            ->input ('會員名稱', function ($val) { $ids = User::getArray ('id', array ('where' => array ('name LIKE ?', '%' . $val . '%'))); return Where::create ('user_id IN (?)',$ids ? $ids : array (0)); }, 'text')
                            ->input ('會員信箱', function ($val) { $ids = User::getArray ('id', array ('where' => array ('email LIKE ?', '%' . $val . '%'))); return Where::create ('user_id IN (?)',$ids ? $ids : array (0)); }, 'text')
                            ;

    $total = AdvMessage::count ($where);
    $page  = Pagination::info ($total);
    $objs  = AdvMessage::find ('all', array (
               'include' => array ('user'),
               'order' => Restful\Order::desc ('id'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total);

    return $this->view->setPath('admin/AdvMessages/index.php')
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
    if ($error = advMessage::getTransactionError (function ($obj) { return $obj->destroy (); }, $obj))
      return refresh (RestfulURL::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulURL::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function show($obj) {
    return $this->view->setPath('admin/advMessages/show.php');
  }
}
