<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class JobTool extends Controller{

  public $checkUrl = null;
  public $cashUrl = null;

  public function __construct () {
    parent::__construct ();

    Load::lib('phpQuery.php');

    $this->checkUrl = 'https://tw.rter.info/json.php?t=currency&q=check&iso=';
    $this->cashUrl = 'https://tw.rter.info/json.php?t=currency&q=cash&iso=';
  }

  public static function create() {
    return new JobTool();
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
      $bankContainer = [];
      foreach( $checkContents['data'] as $checkContent ) {
        $query = phpQuery::newDocument ($checkContent[0]);
        $bankName = trim( pq ("a", $query)->text () );
        if( !isset($bankContainer[$bankName]) ) {
          if( !$bank = Bank::find_by_name($bankName) )
            if( !$bank = Bank::create( array( 'name' => $bankName, 'enable' => Bank::ENABLE_ON ) ) )
              return false;
          $bankContainer[$bankName] = $bank->id;
        }

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
    return true;
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
    exit;
  }
}
