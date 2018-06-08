<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Cli extends Controller {

  public $checkUrl = null;
  public $cashUrl = null;

  public function __construct () {
    parent::__construct ();

    if (!request_is_cli ())
      gg ('你不是 Command Line 指令！！');

    ini_set ('memory_limit', '2048M');
    ini_set ('set_time_limit', 60 * 60);

    Load::lib('phpQuery.php');

    $this->checkUrl = 'https://tw.rter.info/json.php?t=currency&q=check&iso=';
    $this->cashUrl = 'https://tw.rter.info/json.php?t=currency&q=cash&iso=';
  }

  public function updateRecord() {
    if( !$currencies = Currency::find('all', array('where' => array('enable = ?', Currency::ENABLE_ON) ) ) )
      $this->errorOutput(__METHOD__, '查無貨幣table資料');

    foreach( $currencies as $currency ) {

      if( !$checkContents = file_get_contents($this->checkUrl . $currency->iso) )
        $this->errorOutput(__METHOD__, '查無' . $currency->name . '牌告匯率');

      if( !$cashContents = file_get_contents($this->cashUrl . $currency->iso) )
        $this->errorOutput(__METHOD__, '查無' . $currency->name . '現鈔匯率');

      $checkContents = json_decode($checkContents, true);
      $cashContents = json_decode($cashContents, true);

      echo "貨幣ID: " . $currency->id . "\r\n";
      echo "=======================================\r\n";
      // print_R($checkContents['data']);
      $bankContainer = [];
      foreach( $checkContents['data'] as $checkContent ) {
        $query = phpQuery::newDocument ($checkContent[0]);
        $bankName = trim( pq ("a", $query)->text () );
        echo "1111111111111\r\n";
        echo $bankName . "\r\n";

        // $bank = Bank::find_by_name($bankName);
        // $bank = Bank::create( array( 'name' => $bankName, 'enable' => Bank::ENABLE_ON ) );
        print_R( array( 'name' => $bankName, 'enable' => Bank::ENABLE_ON ) );

        if( !isset($bankContainer[$bankName]) ) {
          if( !$bank = Bank::find_by_name($bankName) )
            if( !$bank = Bank::create( array( 'name' => $bankName, 'enable' => Bank::ENABLE_ON ) ) )
              return false;
          $bankContainer[$bankName] = $bank->id;
          echo "222222222222222\r\n";
        }
        echo "==========================123\r\n";
        die;
        $passbookTimes[] = date('Y') . '-' . str_replace('/', '-', $checkContent[3]);
        $passbookRecords[] =array(
          'currency_id' => $currency->id,
          'bank_id' => $bankContainer[$bankName],
          'buy' => $checkContent[1],
          'sell' => $checkContent[2],
        );
        echo "暫存資料外匯牌告 -> 銀行: " . $bankName . "\r\n";
      }

      foreach( $cashContents['data'] as $cashContent ) {
        $query = phpQuery::newDocument ($cashContent[0]);
        $bankName = trim( pq ("a", $query)->text () );
        if( !isset($bankContainer[$bankName]) ) {
          if( !$bank = Bank::find_by_name($bankName) )
            if( !$bank = Bank::create( array( 'name' => $bankName, 'enable' => Bank::ENABLE_ON ) ) )
              return false;
          $bankContainer[$bankName] = $bank->id;
        }

        $cashTimes[] = date('Y') . '-' . str_replace('/', '-', $cashContent[3]);
        $cashRecords[] =array(
          'currency_id' => $currency->id,
          'bank_id' => $bankContainer[$bankName],
          'buy' => $cashContent[1],
          'sell' => $cashContent[2],
        );
        echo "暫存資料外匯牌告 -> 銀行: " . $bankName . "\r\n";
      }
    }

    $transactionPass = function ($passbookTimes, $passbookRecords) {
      foreach ( $passbookRecords as $key => $passbookRecord ) {
        if ( !$time = CurrencyTime::create( array('datetime' => $passbookTimes[$key]) ) )
          return false;
        if ( !PassbookRecord::create( array_merge( $passbookRecord, array('currency_time_id' => $time->id) ) ) )
          return false;
        echo "牌告新增成功 -> 貨幣ID: " . $passbookRecord['currency_id'] . " |  銀行ID: " . $passbookRecord['bank_id'] . "\r\n";
      }
      return true;
    };

    $transactionCash = function ($cashTimes, $cashRecords) {
      foreach ( $cashRecords as $key => $cashRecord ) {
        if ( !$time = CurrencyTime::create( array('datetime' => $cashTimes[$key]) ) )
          return false;
        if ( !CashRecord::create( array_merge( $cashRecord, array('currency_time_id' => $time->id) ) ) )
          return false;
        echo "現鈔新增成功 -> 貨幣ID: " . $cashRecord['currency_id'] . " |  銀行ID: " . $cashRecord['bank_id'] . "\r\n";
      }
      return true;
    };

    if ($error = PassbookRecord::getTransactionError ($transactionPass, $passbookTimes, $passbookRecords))
      exit('新增passbook_records資料表錯誤');

    if ($error = CashRecord::getTransactionError ($transactionCash, $cashTimes, $cashRecords))
      exit('新增cash_records資料表錯誤');

    echo "執行" . __METHOD__ . " success";
  }

  public function currency() {
    $bankQuery = $this->getHtml('https://tw.rter.info/bank/');
    $items = pq(".dropdown-menu", $bankQuery)->eq(0)->find('li');
    $length = $items->length();

    $currencies = [];
    for( $i = 0; $i < $length; $i++ ) {
      $value = explode(' ', trim($items->eq($i)->find('a')->text()));
      $currencies[] = array(
        'name' => $value[1],
        'iso' => $value[0],
        'enable' => Currency::ENABLE_ON,
      );
    }

    $transaction = function ($currencies) {
      foreach ( $currencies as $currency )
        if ( !Currency::create($currency) )
          return false;
      return true;
    };

    if ($error = Currency::getTransactionError ($transaction, $currencies))
      exit('新增currency資料表錯誤');

    return true;
  }

  private function getHtml( $url) {
    if(empty($url))
      return false;

    if (!($get_html_str = str_replace ('&amp;', '&', urldecode (file_get_contents ($url)))))
      exit ('取不到原始碼！');

    return phpQuery::newDocument ($get_html_str);
  }

  public function errorOutput($method, $text) {
    $output = '[' . date('Y-m-d H:i:s') . '] 函式：' . $method . '錯誤，原因：' . $text;
    echo $output;
    die;
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
