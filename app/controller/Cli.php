<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Cli extends Controller {
  public function __construct () {
    parent::__construct ();

    if (!request_is_cli ())
      gg ('你不是 Command Line 指令！！');

    ini_set ('memory_limit', '2048M');
    ini_set ('set_time_limit', 60 * 60);
  }

  public function backupDB () {
    if (!$backup = Backup::create (array ('file' => '', 'size' => 0, 'type' => Backup::TYPE_DATABASE, 'status' => Backup::STATUS_FAILURL, 'read' => Backup::READ_NO, 'time_at' => date ('Y-m-d H:i:s'))))
      gg ('資料庫建立失敗！');


    Load::sysFunc ('file.php');
    Load::sysFunc ('directory.php');

    $models = directory_map (FCPATH . 'app' . DIRECTORY_SEPARATOR . 'model', 1);
    $models = array_filter ($models, function ($t) { return !(strpos ($t, '_') === 0) && pathinfo ($t, PATHINFO_EXTENSION) === 'php'; });
    $models = array_map (function ($m) { return pathinfo ($m, PATHINFO_FILENAME); }, $models);
    $models = array_filter ($models, function ($m) { return class_exists ($m); });
    $models = array_combine ($models, array_map (function ($m) { return array_map (function ($obj) { return $obj->backup (); }, $m::all ()); }, $models));
    
    if (!write_file ($path = FCPATH . 'tmp' . DIRECTORY_SEPARATOR . 'backup_' . Backup::TYPE_DATABASE . '_' . date ('YmdHis') . '.json', json_encode ($models)))
      gg ('寫入檔案失敗！');

    $backup->size = filesize ($path);

    if (!$backup->file->put ($path))
      gg ('上傳檔案失敗！');

    $backup->status = Backup::STATUS_SUCCESS;
    $backup->read = Backup::READ_YES;
    $backup->save ();
  }
  
  public function backupQueryLog ($day = 1) {
    if (!$backup = Backup::create (array ('file' => '', 'size' => 0, 'type' => Backup::TYPE_QUERY_LOG, 'status' => Backup::STATUS_FAILURL, 'read' => Backup::READ_NO, 'time_at' => date ('Y-m-d H:i:s', strtotime (date ('Y-m-d H:i:s') . '-' . $day . 'day')))))
      gg ('資料庫建立失敗！');

    Load::sysFunc ('file.php');
    Load::sysFunc ('directory.php');

    if (!is_readable ($path = FCPATH . 'log' . DIRECTORY_SEPARATOR . 'query-' . date ('Y-m-d', strtotime (date ('Y-m-d') . '-' . $day . 'day')) . '.log'))
      gg ('檔案不存在或不可讀取！');

    $backup->size = filesize ($path);

    if (!$backup->file->put ($path))
      gg ('上傳檔案失敗！');

    $backup->status = Backup::STATUS_SUCCESS;
    $backup->read = Backup::READ_YES;
    $backup->save ();
  }
  
  public function backupLog ($day = 1) {
    if (!$backup = Backup::create (array ('file' => '', 'size' => 0, 'type' => Backup::TYPE_LOG, 'status' => Backup::STATUS_FAILURL, 'read' => Backup::READ_NO, 'time_at' => date ('Y-m-d H:i:s', strtotime (date ('Y-m-d H:i:s') . '-' . $day . 'day')))))
      gg ('資料庫建立失敗！');

    Load::sysFunc ('file.php');
    Load::sysFunc ('directory.php');

    $logs = array ('log-info', 'log-warning', 'log-error');
    $logs = array_combine ($logs, array_map (function ($l) use ($day) { return FCPATH . 'log' . DIRECTORY_SEPARATOR . $l . '-' . date ('Y-m-d', strtotime (date ('Y-m-d') . '-' . $day . 'day')) . '.log'; }, $logs));
    $logs = array_filter ($logs, function ($l) { return is_readable ($l); });
    $logs = array_map (function ($l) { return read_file ($l); }, $logs);

    if (!write_file ($path = FCPATH . 'tmp' . DIRECTORY_SEPARATOR . 'backup_' . Backup::TYPE_LOG . '_' . date ('YmdHis') . '.json', json_encode ($logs)))
      gg ('寫入檔案失敗！');

    $backup->size = filesize ($path);

    if (!$backup->file->put ($path))
      gg ('上傳檔案失敗！');

    $backup->status = Backup::STATUS_SUCCESS;
    $backup->read = Backup::READ_YES;
    $backup->save ();

    $logs = array_keys ($logs);
    $logs = array_combine ($logs, array_map (function ($l) { return FCPATH . 'log' . DIRECTORY_SEPARATOR . $l . '-' . date ('Y-m-d', strtotime (date ('Y-m-d') . '-1day')) . '.log'; }, $logs));
    $logs = array_filter ($logs, function ($l) { return !(is_readable ($l) && @unlink ($l)); });

    if ($logs)
      gg ('刪除檔案失敗！');
  }
}
