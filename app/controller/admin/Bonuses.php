<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Bonuses extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();
    $this->layout->with ('title', '獎金列表')
                 ->with ('current_url', RestfulURL::url ('admin/Bonuses@index'));
  }

  public function index () {
    $where = Where::create();

    $search = Restful\Search::create ($where)
                            ->input ('標題', function ($val) {
                              $advIds = Adv::getArray ('id', array ('where' => Where::create ('title LIKE ?', '%' . $val . '%')));
                              return $where = Where::create ('adv_id IN (?)', $advIds) ? $where : Where::create();
                            }, 'text');

    $total = Bonus::count ($where);
    $page  = Pagination::info ($total);
    $objs  = Bonus::find ('all', array (
               'include' => array ('user'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total);

    return $this->view->setPath('admin/Bonuses/index.php')
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

  public function destroy($obj) {

  }
}
