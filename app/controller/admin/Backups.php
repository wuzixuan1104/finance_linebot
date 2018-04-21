<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Backups extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();
    $this->layout->with ('title', '每日備份')
                 ->with ('current_url', RestfulURL::url ('admin/Backups@index'));
  }

  public function index() {
    Load::sysFunc ('number.php');

    $where = Where::create ();
    $search = Restful\Search::create ($where)
                            ->input ('名稱', function ($val) { return Where::create ('name LIKE ?', '%' . $val . '%'); }, 'text')
                            ->select ('開啟狀態', function ($val) { return Where::create ('status = ?', $val); },
                              isset( Backup::$statusTexts ) ? array_map( function($value, $text) {
                                return array( 'text' => $text, 'value' => $value);
                              }, array_keys(Backup::$statusTexts), Backup::$statusTexts ): array()
                            );

    $total = Backup::count ($where);
    $page  = Pagination::info ($total);
    $objs  = Backup::find ('all', array (
               'order' => Restful\Order::desc ('id'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total);

    return $this->view->setPath('admin/Backups/index.php')

                      ->with ('search', $search)
                      ->with ('pagination', implode ('', $page['links']));
  }

  public function add() {}

  public function create() {}

  public function edit($obj) {}

  public function update($obj) {}

  public function destroy($obj) {}

  public function show($obj) {}

  public function read ($obj) {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'read', '已讀', Backup::READ_YES)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Backup::$readTexts));
    };

    $transaction = function ($posts, $obj) {
      return $obj->columnsUpdate ($posts)
          && $obj->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    if ($error = Backup::getTransactionError ($transaction, $posts, $obj))
      return Output::json ($error, 400);

    return Output::json (array (
        'read' => $obj->read
      ));
  }
}
