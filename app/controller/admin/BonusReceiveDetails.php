<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class BonusReceiveDetails extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();

    $this->layout->with ('title', '獎金領取詳細列表')
                 ->with ('current_url', RestfulURL::url ('admin/BonusReceives@index'));
  }

  public function index () {
    $where = Where::create ('bonus_receive_id = ?', $this->parent->id);
    $search = Restful\Search::create ($where);
    $total = BonusReceiveDetail::count ($where);
    $page  = Pagination::info ($total);
    $objs  = BonusReceiveDetail::find ('all', array (
               // 'include' => array ('user'),
               'order' => Restful\Order::desc ('id'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total);

    return $this->view->setPath('admin/BonusReceiveDetails/index.php')
                      ->with ('search', $search)
                      ->with ('pagination', implode ('', $page['links']));
  }

  public function create() {
  }

  public function add() {
  }

  public function edit($obj) {
  }

  public function update($obj) {
  }

  public function show($obj) {
  }

  public function destroy ($obj) {
  }
}
