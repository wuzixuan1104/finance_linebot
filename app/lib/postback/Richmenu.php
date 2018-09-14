<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Load::lib('MyLineBot.php');

class Search {

  public static function create() {
    if(!$currencies = \M\Currency::all(['where' => ['enable = ?', \M\Currency::ENABLE_ON]]) )
      return false;

    $currencies = array_chunk($currencies, 5);

    $flexes = [];
    $bubbles = [];
    foreach($currencies as $currency) {
      foreach($currency as $v) {
        $flexes[] = FlexBox::create([
                      FlexBox::create([FlexText::create($v->name)])->setLayout('vertical')->setFlex(7),
                      FlexSeparator::create(),
                      FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('選擇', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'bank', 'param' => ['currencyId' => $v->id]]), $v->name))
                    ])->setLayout('horizontal')->setSpacing('md');

        $flexes[] = FlexSeparator::create();
      }

      $bubbles[] = FlexBubble::create([
                    'header' => FlexBox::create([FlexText::create('選擇貨幣')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                    'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                    'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                  ]);
      $flexes = [];
    }

    return MyLineBotMsg::create()->flex('貨幣類別', FlexCarousel::create($bubbles)); 
  }

  public static function bank($params) {
    if(!(isset($params['currencyId']) || $params['currencyId']))
      return false;

    $records = [];
    if($passbooks = \M\PassbookRecord::all(['where' => ["( bankId, currencyId, createAt ) in ( select `bankId`, `currencyId`, max(`createAt`) from `PassbookRecord` where `currencyId` = ? group by `bankId` ) ", $params['currencyId']] ]))
      array_map( function($v) use(&$records) { return $records[$v->bankId] = $v->bank->name; }, $passbooks);

    if($cashes = \M\CashRecord::all(['where' => ["( bankId, currencyId, createAt ) in ( select `bankId`, `currencyId`, max(`createAt`) from `CashRecord` where `currencyId` = ? group by `bankId` ) ", $params['currencyId']] ]))
      array_map( function($v) use(&$records) { return $records[$v->bankId] = $v->bank->name; }, $cashes);

    if(!$records)
      return false;

    $flexes = $bubbles = [];
    $cnt = 0;
    foreach($records as $k => $v) {
      $flexes[] = FlexBox::create([
                    FlexBox::create([FlexText::create($v)])->setLayout('vertical')->setFlex(7),
                    FlexSeparator::create(),
                    FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('選擇', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'show', 'param' => ['currencyId' => $params['currencyId'], 'bankId' => $k]]), $v))
                  ])->setLayout('horizontal')->setSpacing('md');
      $flexes[] = FlexSeparator::create();

      if(++$cnt % 5 == 0) {
        $bubbles[] = FlexBubble::create([
                    'header' => FlexBox::create([FlexText::create('選擇銀行')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                    'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                    'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                  ]);
        $flexes = [];
      }
    }

    if($flexes) {
      $bubbles[] = FlexBubble::create([
                    'header' => FlexBox::create([FlexText::create('選擇銀行')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                    'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                    'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                  ]);
    }
    return MyLineBotMsg::create()->flex('選擇銀行', FlexCarousel::create($bubbles));
  }

  public static function show($params, $source) {
    if(!(isset($params['currencyId'], $params['bankId']) && $params['currencyId'] && $params['bankId']))
      return false;

    if( !$currency = \M\Currency::one('id = ?', $params['currencyId']) )
      return false;

    if( !$bank = \M\Bank::one('id = ?', $params['bankId']) )
      return false;

    $condition = ['where' => ['bankId = ? and currencyId = ?', $params['bankId'], $params['currencyId']], 'order' => 'createAt desc', 'limit' => 1 ];
    
    $bubbles = $tmp = [];

    if($passbook = \M\PassbookRecord::one($condition)) 
        $tmp['牌告匯率'] = $passbook;

    if($cash = \M\CashRecord::one($condition)) 
        $tmp['現金匯率'] = $cash;

    foreach($tmp as $k => $v) {
      $time = (string)(($time = \M\CurrencyTime::one(['id = ?', $v->currencyTimeId])) ? $time->datetime : $v->createAt);
      
      $bubbles[] = FlexBox::create([FlexText::create($k)->setColor('#906768'), FlexText::create($time)->setSize('xxs')->setAlign('end')->setColor('#bbbbbb')])->setLayout('horizontal');
      $bubbles[] = FlexSeparator::create();
      $bubbles[] = FlexBox::create([
                    FlexBox::create([
                      FlexBox::create([FlexText::create('賣出：' . $v->sell)])->setLayout('vertical')
                    ])->setLayout('vertical')->setFlex(5),
                    
                    FlexSeparator::create(),

                    FlexBox::create([
                      FlexBox::create([FlexText::create('買入：' . $v->buy)])->setLayout('vertical')
                    ])->setLayout('vertical')->setFlex(5)
                  ])->setLayout('horizontal')->setSpacing('md');

    }
   
    return MyLineBotMsg::create()->flex('問題類別', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create($currency->name . ' / ' . $bank->name)->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create($bubbles)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'footer' => FlexBox::create([FlexButton::create('primary')->setColor('#f97172')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('匯率試算', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'type', 'param' => ['currencyId' => $params['currencyId'], 'bankId' => $params['bankId']]]), null))])->setLayout('horizontal')->setSpacing('xs'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]));
  }
}

class Calculate {
  public static function create() {

  }
}

class History {

}