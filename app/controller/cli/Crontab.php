<?php defined ('MAPLE') || exit ('此檔案不允許讀取。');

class Crontab extends Controller{

  public $checkUrl = null;
  public $cashUrl = null;

  public function __construct () {
    parent::__construct ();

    Load::lib('phpQuery.php');

    $this->checkUrl = 'https://tw.rter.info/json.php?t=currency&q=check&iso=';
    $this->cashUrl = 'https://tw.rter.info/json.php?t=currency&q=cash&iso=';
  }

  public function updateRecord() {
    if( !$currencies = \M\Currency::all(['where' => ['enable = ?', \M\Currency::ENABLE_ON] ] ) )
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
          if( !$bank = \M\Bank::one(['where' => ['name = ?', $bankName]]) )
            if( !$bank = \M\Bank::create(['name' => $bankName, 'enable' => \M\Bank::ENABLE_ON]) )
              return false;
          $bankContainer[$bankName] = $bank->id;
        }

        $passbookTimes[] = date('Y') . '-' . str_replace('/', '-', $checkContent[3]) . ':00';

        $passbookRecords[] = [
          'currencyId' => $currency->id,
          'bankId' => $bankContainer[$bankName],
          'buy' => $checkContent[1],
          'sell' => $checkContent[2],
        ];
        echo "暫存資料外匯牌告 -> 銀行: " . $bankName . "\r\n";
      }

      foreach( $cashContents['data'] as $cashContent ) {
        $query = phpQuery::newDocument ($cashContent[0]);
        $bankName = trim( pq ("a", $query)->text () );
        if( !isset($bankContainer[$bankName]) ) {
          if( !$bank = \M\Bank::one(['where' => ['name = ?', $bankName]]) )
            if( !$bank = \M\Bank::create(['name' => $bankName, 'enable' => \M\Bank::ENABLE_ON ]) )
              return false;
          $bankContainer[$bankName] = $bank->id;
        }

        $cashTimes[] = date('Y') . '-' . str_replace('/', '-', $cashContent[3]) . ':00';;
        $cashRecords[] = [
          'currencyId' => $currency->id,
          'bankId' => $bankContainer[$bankName],
          'buy' => $cashContent[1],
          'sell' => $cashContent[2],
        ];
        echo "暫存資料外匯現金 -> 銀行: " . $bankName . "\r\n";
      }
    }

    transaction( function() use ($passbookTimes, $passbookRecords) {
      foreach ( $passbookRecords as $key => $passbookRecord ) {
        if ( !$time = \M\CurrencyTime::one(['where' => ['datetime = ?', $passbookTimes[$key]]]) )
          if( !$time = \M\CurrencyTime::create(['datetime' => $passbookTimes[$key]]) )
            error('新增passbook_records資料表錯誤');

        if ( !\M\PassbookRecord::create( array_merge( $passbookRecord, ['currencyTimeId' => $time->id] ) ) )
          error('新增passbook_records資料表錯誤');
        echo "牌告新增成功 -> 貨幣ID: " . $passbookRecord['currencyId'] . " |  銀行ID: " . $passbookRecord['bankId'] . "\r\n";
      }
      return true;
    });

    transaction(function() use ($cashTimes, $cashRecords) {
      foreach ( $cashRecords as $key => $cashRecord ) {
        if ( !$time = \M\CurrencyTime::one(['where' => ['datetime = ?', $cashTimes[$key]]]) )
          if( !$time = \M\CurrencyTime::create(['datetime' => $cashTimes[$key]]) )
            error('新增cash_records資料表錯誤');
        if ( !\M\CashRecord::create( array_merge( $cashRecord, ['currencyTimeId' => $time->id] ) ) )
          error('新增cash_records資料表錯誤');
        echo "現鈔新增成功 -> 貨幣ID: " . $cashRecord['currencyId'] . " |  銀行ID: " . $cashRecord['bankId'] . "\r\n";
      }
      return true;
    });

    echo "執行" . __METHOD__ . " success";
  }

  public function currency() {
    $bankQuery = $this->getHtml('https://tw.rter.info/bank/');
    $items = pq(".dropdown-menu", $bankQuery)->eq(0)->find('li');
    $length = $items->length();
    $enableCurrency = ['美國(美金)', '歐元區(歐元)', '日本(日幣)', '中國香港(港幣)', '英國(英鎊)', '中國(人民幣)', '中國(離岸人民幣)', '南韓(韓元)', '澳大利亞(澳幣)', '紐西蘭(紐幣)', '新加坡(新加坡幣)', '泰國(泰銖)', '馬來西亞(馬來幣)', '越南(越南盾)', '中國澳門(澳門幣)'];
    $currencies = [];
    for( $i = 0; $i < $length; $i++ ) {
      $value = explode(' ', trim($items->eq($i)->find('a')->text()));

      $currencies[] = array(
        'name' => $value[1],
        'iso' => $value[0],
        'enable' => in_array($value[1], $enableCurrency) ? \M\Currency::ENABLE_ON : \M\Currency::ENABLE_OFF,
      );
    }

    transaction(function() use ($currencies) {
      foreach ( $currencies as $currency )
        if ( !\M\Currency::create($currency) )
          error('新增currency資料表錯誤');
      return true;
    });

    echo "執行" . __METHOD__ . " success";
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

  /* 1. 抓取今日貨幣比對銀行最高和最低存入history_records
   * 2. 其他刪除也要刪除對應的currency_time
   */
  public function forexRecordJob() {

    transaction(function() {
      $maxDate = \M\PassbookRecord::one(['select' => 'max(`createAt`) as date']);
      $date = $maxDate->date;
      if( $maxPasses = \M\PassbookRecord::all(['where' => ["(`currencyId`, `bankId`, `sell`) in ( select `currencyId`, `bankId`, max(`sell`) as sell from `PassbookRecord` where date(createAt) = date(now()) group by `currencyId`, `bankId`)"], 'group' => '`currencyId`, `bankId`' ]) ) {
        foreach( $maxPasses as $maxPass ) {
          $param = [
            'kind' => \M\PassbookHistory::KIND_MAX,
            'currencyId' => $maxPass->currencyId,
            'currencyTimeId' => $maxPass->currencyTimeId,
            'bankId' => $maxPass->bankId,
            'buy' => $maxPass->buy,
            'sell' => $maxPass->sell,
          ];

          if( !\M\PassbookHistory::create($param) )
            return false;
        }
      }
      if( $minPasses = \M\PassbookRecord::all(['where' => ["(`currencyId`, `bankId`, `sell`) in ( select `currencyId`, `bankId`, min(`sell`) as sell from `PassbookRecord` where date(createAt) = date(now()) group by `currencyId`, `bankId`)"], 'group' => '`currencyId`, `bankId`']) ) {

        foreach( $minPasses as $minPass ) {
          $param = [
            'kind' => \M\PassbookHistory::KIND_MIN,
            'currencyId' => $minPass->currencyId,
            'currencyTimeId' => $minPass->currencyTimeId,
            'bankId' => $minPass->bankId,
            'buy' => $minPass->buy,
            'sell' => $minPass->sell,
          ];

          if( !\M\PassbookHistory::create($param) )
            return false;
        }
      }

      if( !\M\PassbookRecord::deleteAll( ['where' => ['createAt != ?', $date]] ) )
        return false;

      return true;
    });
    
    transaction(function() {
      $maxDate = \M\CashRecord::one(['select' => 'max(`createAt`) as date']);
      $date = $maxDate->date;

      if( $maxPasses = \M\CashRecord::all(['where' => ["(`currencyId`, `bankId`, `sell`) in ( select `currencyId`, `bankId`, max(`sell`) as sell from `CashRecord` where date(createAt) = date(now()) group by `currencyId`, `bankId`)"], 'group' => '`currencyId`, `bankId`']) ) {
        foreach( $maxPasses as $maxPass ) {
          $param = [
            'kind' => \M\CashHistory::KIND_MAX,
            'currencyId' => $maxPass->currencyId,
            'currencyTimeId' => $maxPass->currencyTimeId,
            'bankId' => $maxPass->bankId,
            'buy' => $maxPass->buy,
            'sell' => $maxPass->sell,
          ];

          if( !\M\CashHistory::create($param) )
            return false;
        }
      }

      if( $minPasses = \M\CashRecord::all(['where' => ["(`currencyId`, `bankId`, `sell`) in ( select `currencyId`, `bankId`, min(`sell`) as sell from `CashRecord` where date(createAt) = date(now()) group by `currencyId`, `bankId`)"], 'group' => '`currencyId`, `bankId`']) ) {
        foreach( $minPasses as $minPass ) {
          $param = [
            'kind' => \M\CashHistory::KIND_MIN,
            'currencyId' => $minPass->currencyId,
            'currencyTimeId' => $minPass->currencyTimeId,
            'bankId' => $minPass->bankId,
            'buy' => $minPass->buy,
            'sell' => $minPass->sell,
          ];

          if( !\M\CashHistory::create($param) )
            return false;
        }
      }

      if( !\M\CashRecord::deleteAll(['where' => ['createAt != ?', $date]]) )
        return false;

      return true;
    });

    echo 'sucess';
  }
}
