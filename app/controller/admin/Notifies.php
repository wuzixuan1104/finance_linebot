<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Notifies extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();

    $this->layout->with ('title', '帳號列表')
                 ->with ('current_url', RestfulURL::url ('admin/Notifies@index'));
  }

  public function index() {
    $where = Where::create();

    $search = Restful\Search::create ($where)
                            ->input ('ID', function ($val) { return Where::create ('id = ?', $val); }, 'text')
                            ->input ('帳號名稱', function ($val) { return Where::create ('name LIKE ?', '%' . $val . '%'); }, 'text')
                            ->input ('銀行分行', function ($val) { return Where::create ('bank_branch LIKE ?', '%' . $val . '%'); }, 'text')
                            ->input ('銀行帳號', function ($val) { return Where::create ('bank_account LIKE ?', '%' . $val . '%'); }, 'text')
                            ->input ('電話', function ($val) { return Where::create ('phone LIKE ?', '%' . $val . '%'); }, 'text')
                            ;

    $total = Account::count ($where);
    $page  = Pagination::info ($total);
    $objs  = Account::find ('all', array (
               // 'include' => array ('user'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total);

    return $this->view->setPath('admin/Accounts/index.php')
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
  }

  public function show($obj) {
  }

}
