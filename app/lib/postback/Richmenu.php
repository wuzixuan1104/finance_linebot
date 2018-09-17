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

    if(!$currency = \M\Currency::one('id = ?', $params['currencyId']))
      return false;

    if(!$bank = \M\Bank::one('id = ?', $params['bankId']))
      return false;

    if($calcRecord = \M\CalcRecord::one('sourceId = ? and currencyId = ? and bankId = ?', $source->id, $currency->id, $bank->id))
      ($calcRecord->updateAt = date('Y-m-d H:i:s')) && $calcRecord->save();
    elseif(\M\CalcRecord::count('sourceId = ?', $source->id) < 10)
      if(!\M\CalcRecord::create(['sourceId' => $source->id, 'currencyId' => $currency->id, 'bankId' => $bank->id]))
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
      $bubbles[] = FlexSeparator::create();
    }
   
    return MyLineBotMsg::create()->flex('問題類別', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create($currency->name . ' / ' . $bank->name)->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create($bubbles)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'footer' => FlexBox::create([FlexButton::create('primary')->setColor('#f97172')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('匯率試算', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'type', 'param' => ['curName' => $currency->name, 'passbookSell' => $passbook ? $passbook->sell : 0, 'cashSell' => $cash ? $cash->sell : 0]]), null))])->setLayout('horizontal')->setSpacing('xs'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]));
  }
}

class Calculate {
  public static function create($params, $source) {
    if(!$source )
      return false;

    ($source->action = '') && $source->save();

    if(!$calcs = \M\CalcRecord::all(['where' => ['sourceId = ?', $source->id], 'order' => 'updateAt DESC']))
      return MyLineBotMsg::create()->text('尚無匯率試算資料，請點選下方"匯率查詢"查詢您要試算的匯率！'); 

    $flexes = $bubbles = [];
    $cnt = 0;
    foreach($calcs as $calc) {
      $condition = ['where' => ['bankId = ? and currencyId = ?', $calc->bankId, $calc->currencyId], 'order' => 'createAt desc', 'limit' => 1 ];
      $passbook = \M\PassbookRecord::one($condition);
      $cash = \M\CashRecord::one($condition); 

      $flexes[] = FlexBox::create([
                    FlexBox::create([
                      FlexText::create($calc->currency->name)->setColor('#906768')->setSize('md'),
                      FlexText::create('/ '. $calc->bank->name)->setSize('md'),
                    ])->setLayout('vertical')->setFlex(4),

                    FlexSeparator::create(),

                    FlexButton::create('primary')->setFlex(3)->setColor('#d4d4d4')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback( '移除', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'delete', 'param' => ['calcRecordId' => $calc->id]]), '移除')),
                    FlexButton::create('primary')->setFlex(3)->setColor('#f37370')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback( '試算', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'type', 'param' => ['curName' => $calc->currency->name, 'passbookSell' => $passbook ? $passbook->sell : 0, 'cashSell' => $cash ? $cash->sell : 0]]), '試算')),
                    

                ])->setLayout('horizontal')->setSpacing('md')->setMargin('lg');
      $flexes[] = FlexSeparator::create();

      if(++$cnt % 5 == 0) {
        $bubbles[] = FlexBubble::create([
                      'header' => FlexBox::create([FlexText::create('匯率試算')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                      'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                      'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                    ]);
        $flexes = [];
      }
    }

    if($flexes) {
      $bubbles[] = FlexBubble::create([
                    'header' => FlexBox::create([FlexText::create('匯率試算')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                    'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                    'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                  ]);
    }
 
    return MyLineBotMsg::create()->flex('匯率試算', FlexCarousel::create($bubbles));
  }

  public static function delete($params, $source) {
    if(isset($params['calcRecordId']) && $obj = \M\CalcRecord::one('id = ?', $params['calcRecordId']))
      if(!$obj->delete())
        return false;

    return self::create($params, $source);
  }

  public static function type($params, $source) {
    if(!(isset($params['curName'], $params['passbookSell'], $params['cashSell']) && $params['curName']))
      return false; 

    return MyLineBotMsg::create()->flex('試算模式', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create('選擇試算模式')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create([
                FlexButton::create('primary')->setColor('#f1c87f')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback( $params['curName']. ' -> 台幣', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'input', 'param' => ['calc' => 'A', 'curName' => $params['curName'], 'passbookSell' => $params['passbookSell'], 'cashSell' => $params['cashSell']]]), $params['curName'] . ' -> 台幣')),
                FlexButton::create('primary')->setColor('#f97172')->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('台幣 -> ' . $params['curName'], json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'input', 'param' => ['calc' => 'B', 'curName' => $params['curName'], 'passbookSell' => $params['passbookSell'], 'cashSell' => $params['cashSell']]]), '台幣 -> ' . $params['curName'])),

            ])->setLayout('vertical'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]));
  }

  public static function checkout($params, $source) {
    if(!$action = json_decode($source->action, true))
      return false;

    ($action['calc'] = $action['calc'] == 'A' ? 'B' : 'A') && ($source->action = json_encode($action)) && $source->save();

    return MyLineBotMsg::create()->text('請輸入試算金額'); 
  }

  public static function input($params, $source) {
    if(!(isset($params['calc'], $params['curName'], $params['passbookSell'], $params['cashSell']) && $source && $params['calc'] && $params['curName'] && $params['passbookSell'] && $params['cashSell']))
      return false; 
    ($source->action = json_encode($params)) && $source->save();

    return MyLineBotMsg::create()->text('請輸入試算金額');
  }

  public static function show($input, $source) {
    if(!($input && ($action = json_decode($source->action, true) )))
      return false;
    if(!isset($action['calc']))
      return false;
    
    $bubbles = [];
    if($action['passbookSell']) {
      $money = $action['calc'] == 'A' ? round($input * $action['passbookSell'], 3) : round($input / $action['passbookSell'], 3);

      $bubbles[] = FlexBox::create([
                      FlexText::create('牌告')->setFlex(3),
                      FlexSeparator::create(),
                      FlexText::create((string)$money)->setMargin('lg')->setFlex(7)
                    ])->setLayout('horizontal')->setSpacing('md');
      $bubbles[] = FlexSeparator::create()->setMargin('md');
    }

    if($action['cashSell']) {
      $money = $action['calc'] == 'A' ? round($input * $action['cashSell'], 3) : round($input / $action['cashSell'], 3);
      $bubbles[] = FlexBox::create([
                      FlexText::create('現鈔')->setFlex(3),
                      FlexSeparator::create(),
                      FlexText::create((string)$money)->setMargin('lg')->setFlex(7)
                    ])->setLayout('horizontal')->setSpacing('md')->setMargin("md");
      $bubbles[] = FlexSeparator::create()->setMargin('md');
    }
    $bubbles[] = FlexText::create('ps. 可直接輸入金額再重新試算')->setSize('xs')->setMargin('lg')->setColor('#969696');
    
    $rebtn = '試算' . ($action['calc'] == 'A' ? '台幣兌換' . $action['curName'] : $action['curName'] . '兌換台幣'); 

    return MyLineBotMsg::create()->flex('試算模式', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create($action['calc'] == 'A' ? $action['curName'] . '兌換台幣' : '台幣兌換' . $action['curName'])->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create([FlexText::create('輸入金額：' . $input)->setColor('#906768'), FlexSeparator::create(), FlexBox::create([FlexBox::create($bubbles)->setLayout('vertical')])->setLayout('horizontal')->setSpacing('md')])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'footer' => FlexBox::create([FlexButton::create('primary')->setColor('#f97172')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback($rebtn, json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'checkout', 'param' => []]), $rebtn))])->setLayout('horizontal')->setSpacing('xs'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]));
  }
}

class History {

}