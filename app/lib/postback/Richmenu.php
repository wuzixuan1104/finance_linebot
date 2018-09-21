<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Load::lib('MyLineBot.php');
Load::lib('Common.php');

class Search {
  public static function create() {
    return Common::currency(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'bank', 'param' => []]);
  }

  public static function bank($params) {
    if(!(isset($params['currencyId'])))
      return false;
    return Common::bank($params['currencyId'], ['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'show', 'param' => ['currencyId' => $params['currencyId']]]);
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
    else {
      if(\M\CalcRecord::count('sourceId = ?', $source->id) >= 10)
        if($calc = \M\CalcRecord::one(['where' => ['sourceId = ?', $source->id]]))
          $calc->delete();

      if(!\M\CalcRecord::create(['sourceId' => $source->id, 'currencyId' => $currency->id, 'bankId' => $bank->id]))
        return false;
    }
    

    $condition = ['where' => ['bankId = ? and currencyId = ?', $params['bankId'], $params['currencyId']], 'order' => 'createAt desc', 'limit' => 1 ];
    
    $bubbles = $tmp = [];

    if($passbook = \M\PassbookRecord::one($condition)) 
        $tmp['牌告匯率'] = $passbook;

    if($cash = \M\CashRecord::one($condition)) 
        $tmp['現金匯率'] = $cash;

    foreach($tmp as $k => $v) {
      $time = (string)(($time = \M\CurrencyTime::one('id = ?', $v->currencyTimeId)) ? $time->datetime : $v->createAt);
      
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
   
    return MyLineBotMsg::create()->flex('匯率試算', FlexBubble::create([
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

    ($action['calc'] = $action['calc'] == 'A' ? 'B' : 'A') && ($source->action = json_encode(array_merge($action, ['class' => 'Calculate', 'method' => 'show']))) && $source->save();

    return MyLineBotMsg::create()->text('請輸入試算金額'); 
  }

  public static function input($params, $source) {
    if(!(isset($params['calc'], $params['curName'], $params['passbookSell'], $params['cashSell']) && $source && $params['calc'] && $params['curName']))
      return false; 
    ($source->action = json_encode(array_merge($params, ['class' => 'Calculate', 'method' => 'show']))) && $source->save();

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

class Best {
  public static function create() {
    return Common::currency(['lib' => 'postback/Richmenu', 'class' => 'Best', 'method' => 'show', 'param' => []]);
  }

  public static function show($params) {
    if(!(isset($params['currencyId']) && $params['currencyId']))
      return false;
    
    //以牌告為依據進行排行前五個
    if(!$passbooks = \M\PassbookRecord::all(['where' => ["( bankId, currencyId, createAt ) in ( select `bankId`, `currencyId`, max(`createAt`) from `PassbookRecord` where `currencyId` = ? group by `bankId` ) ", $params['currencyId']], 'order' => 'sell ASC', 'limit' => 5 ]))
      return MyLineBotMsg::create()->text('此貨幣目前查無排行');

    $currencyName = '';
    $tmp = [];
    foreach($passbooks as $passbook) {
      !$currencyName && $currencyName = $passbook->currency->name;

      $cash = \M\CashRecord::one(['where' => ['currencyId = ? and bankId = ?', $params['currencyId'], $passbook->bankId], 'order' => 'sell ASC, createAt DESC']);
      $tmp[] = FlexBox::create([
                FlexText::create($passbook->bank->name)->setFlex(3),
                FlexSeparator::create()->setMargin('md'),
                FlexText::create((string)$passbook->sell)->setFlex(3)->setMargin('lg'),
                FlexSeparator::create()->setMargin('md'),
                FlexText::create((string)($cash ? $cash->sell : ' - '))->setFlex(3)->setMargin('lg'),
              ])->setLayout('horizontal');
    }

    return MyLineBotMsg::create()->flex('最佳匯率排行', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create('最佳匯率排行')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create(array_merge([
              FlexText::create($currencyName)->setColor('#906768'),
              FlexSeparator::create(),
              
              FlexBox::create([
                FlexText::create('銀行')->setFlex(3)->setColor('#969595'),
                FlexSeparator::create()->setMargin('md'),
                FlexText::create('牌告')->setFlex(3)->setMargin('lg')->setColor('#969595'),
                FlexSeparator::create()->setMargin('md'),
                FlexText::create('現鈔')->setFlex(3)->setMargin('lg')->setColor('#969595'),
              ])->setLayout('horizontal'),

              FlexSeparator::create()->setMargin('md'),

            ], $tmp))->setLayout('vertical')->setSpacing('md')->setMargin('sm'),

            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]));
  } 
}

class Remind {
  public static function create() {
    return MyLineBotMsg::create()->flex('匯率提醒', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create('匯率提醒')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create([
              FlexText::create('設定提醒')->setColor('#906768'),
              FlexSeparator::create(),
              FlexBox::create([
                FlexText::create('牌告')->setFlex(3)->setMargin('md'),
                FlexSeparator::create(),
                FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('區間', json_encode(['lib' => 'postback/Richmenu', 'class' => 'RemindRange', 'method' => 'create', 'param' => ['kind' => \M\RemindRange::KIND_PASSBOOK]]), '區間')),
                FlexSeparator::create(),
                FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('浮動', json_encode(['lib' => 'postback/Richmenu', 'class' => 'RemindFloat', 'method' => 'create', 'param' => ['kind' => \M\RemindRange::KIND_PASSBOOK]]), '浮動')),
              ])->setLayout('horizontal')->setSpacing('md'),

              FlexSeparator::create(),

              FlexBox::create([
                FlexText::create('現鈔')->setFlex(3)->setMargin('md'),
                FlexSeparator::create(),
                FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('區間', json_encode(['lib' => 'postback/Richmenu', 'class' => 'RemindRange', 'method' => 'create', 'param' => ['kind' => \M\RemindFloat::KIND_CASH]]), '區間')),
                FlexSeparator::create(),
                FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('浮動', json_encode(['lib' => 'postback/Richmenu', 'class' => 'RemindFloat', 'method' => 'create', 'param' => ['kind' => \M\RemindFloat::KIND_CASH]]), '浮動')),
              ])->setLayout('horizontal')->setSpacing('md'),

              FlexSeparator::create(),

              FlexButton::create('primary')->setColor('#f9b071')->setFlex(3)->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('查看已設定的提醒', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Remind', 'method' => 'show', 'param' => []]), '查看已設定的提醒')),
              FlexButton::create('link')->setColor('#f9b071')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('使用說明', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Remind', 'method' => 'explain', 'param' => []]), '使用說明')),
            ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]));
  }

  public static function show($params, $source) {
    if(!$source) 
      return false;

    $flexes = $bubbles = $lists = [];
    if($ranges = \M\RemindRange::all('sourceId = ?', $source->id)) 
      array_map( function($v) use(&$lists) { return $lists['區間設定'][] = ['id' => $v->id, 'currency' => $v->currency->name, 'bank' => $v->bank ? $v->bank->name : '', 'kind' => \M\RemindRange::KIND[$v->kind], 'type' => $v->type, 'value' => $v->value, 'createAt' => (string)$v->createAt]; }, $ranges);
    
    if($floats = \M\RemindFloat::all('sourceId = ?', $source->id)) 
      array_map( function($v) use(&$lists) { return $lists['浮動設定'][] = ['id' => $v->id, 'currency' => $v->currency->name, 'bank' => $v->bank ? $v->bank->name : '', 'kind' => \M\RemindFloat::KIND[$v->kind], 'value' => $v->value, 'createAt' => (string)$v->createAt]; }, $floats);
    
    if(!$lists)
      return MyLineBotMsg::create()->text('尚未設定任何提醒！');

    $cnt = 0;
    foreach($lists as $k => $list) {
      $flexes[] = FlexText::create('-- ' . $k . ' --')->setColor('#e46767')->setSize('md');
      foreach($list as $v) {
        $flexes[] = FlexSeparator::create();
        $flexes[] = FlexBox::create([
                      FlexBox::create([
                        FlexText::create($v['currency'] . ($v['bank'] ? ' / ' . $v['bank'] : ''))->setColor('#906768')->setFlex(7),
                        FlexButton::create('primary')->setFlex(3)->setColor('#f97172')->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('移除', json_encode(['lib' => 'postback/Richmenu', 'class' => $k == '區間設定' ? 'RemindRange' : 'RemindFloat', 'method' => 'delete', 'param' => ['id' => $v['id']]]), '移除')),
                      ])->setLayout('horizontal'),
                      FlexText::create($v['kind'] . (isset($v['type']) ? ($v['type'] == \M\RemindRange::TYPE_LESS ? ' <= ' : ' >= ') : ' +- ') . $v['value'])->setFlex(7)
                    ])->setLayout('vertical');
        $flexes[] = FlexSeparator::create();
        $flexes[] = FlexText::create($v['createAt'])->setSize('xs')->setAlign('end')->setColor('#bdbdbd');
        
        if(++$cnt % 2 == 0) {
          $bubbles[] = FlexBubble::create([
                        'header' => FlexBox::create([FlexText::create('匯率提醒列表')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                        'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                        'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                      ]);
          $flexes = [];
        }     
      }
    }
    if($flexes) {
      $bubbles[] = FlexBubble::create([
                    'header' => FlexBox::create([FlexText::create('匯率提醒列表')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                    'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                    'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                  ]);
    }

    return MyLineBotMsg::create()->flex('外匯提醒列表', FlexCarousel::create($bubbles));
  }

  public static function explain() {

  }
}

class RemindRange{
  public static function create($params, $source) {
    if(!(isset($params['kind']) && $params['kind']))
      return false;

    if(\M\RemindRange::count(['sourceId = ?', $source->id]) >= 2)
      return MyLineBotMsg::create()->text('已超過上限，最多只能設定2個區間提醒！'); 

    return self::currency(['kind' => $params['kind'], 'bank' => true], $source);

    //目前都必須指定銀行
    // return MyLineBotMsg::create()->flex('試算模式', FlexBubble::create([
    //         'header' => FlexBox::create([FlexText::create('是否指定銀行')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
    //         'body' => FlexBox::create([
    //             FlexButton::create('primary')->setColor('#f1c87f')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback( '是', json_encode(['lib' => 'postback/Richmenu', 'class' => 'RemindRange', 'method' => 'currency', 'param' => ['kind' => $params['kind'], 'bank' => true]]), '是')),
    //             FlexButton::create('primary')->setColor('#f97172')->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('否', json_encode(['lib' => 'postback/Richmenu', 'class' => 'RemindRange', 'method' => 'currency', 'param' => ['kind' => $params['kind'], 'bank' => false]]), '否')),

    //         ])->setLayout('horizontal'),
    //         'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
    //       ]));
  }

  public static function currency($params, $source) {
    if(!(isset($params['kind'], $params['bank']) && $params['kind'] && $source))
      return false;
    return Common::currency(['lib' => 'postback/Richmenu', 'class' => 'RemindRange', 'method' => $params['bank'] ? 'bank' : 'input', 'param' => ['kind' => $params['kind']]]);
  }

  public static function bank($params) {
    if(!(isset($params['currencyId'])))
      return false;
    return Common::bank($params['currencyId'], ['lib' => 'postback/Richmenu', 'class' => 'RemindRange', 'method' => 'input', 'param' => ['currencyId' => $params['currencyId'], 'kind' => $params['kind']]], $params['kind']);
  }

  public static function input($params, $source) {
    ($source->action = json_encode(['class' => 'RemindRange', 'method' => 'choose', 'kind' => $params['kind'], 'currencyId' => $params['currencyId'], 'bankId' => isset($params['bankId']) ? $params['bankId'] : 0])) && $source->save();
    return MyLineBotMsg::create()->text('請輸入區間值'); 
  }

  public static function choose($text, $source) {
    $action = $source->action ? json_decode($source->action, true) : '';

    if($action && \M\RemindRange::one('sourceId = ? and bankId = ? and value = ? and kind = ?', $source->id, $action['bankId'] ? $action['bankId'] : 0, $text, $action['kind']))
      return MyLineBotMsg::create()->text('此區間範圍已設定過，請重新設定'); 

    return MyLineBotMsg::create()->flex('選擇範圍區間', FlexBubble::create([
        'header' => FlexBox::create([FlexText::create('選擇範圍區間')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
        'body' => FlexBox::create([
          FlexText::create('當匯率符合所選範圍時會發出通知')->setColor('#906768'),
          FlexSeparator::create(),
          FlexButton::create('primary')->setColor('#f9b071')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('> = ' . $text, json_encode(['lib' => 'postback/Richmenu', 'class' => 'RemindRange', 'method' => 'success', 'param' => ['text' => $text, 'type' => \M\RemindRange::TYPE_MORE]]), \M\RemindRange::TYPE[M\RemindRange::TYPE_MORE])),
          FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('< = ' . $text, json_encode(['lib' => 'postback/Richmenu', 'class' => 'RemindRange', 'method' => 'success', 'param' => ['text' => $text, 'type' => \M\RemindRange::TYPE_LESS]]), \M\RemindRange::TYPE[M\RemindRange::TYPE_LESS])),
          FlexText::create('ps. 一天至多提醒一次')->setColor('#a5a3a3')->setSize('sm'),
        ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
        'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
      ]));
  }

  public static function delete($params, $source) {
    if(!(isset($params['id']) && $params['id'] && $source))
      return false;

    if($obj = \M\RemindRange::one('id = ?', $params['id']))
      if($obj->delete())
        return Remind::show($params, $source);
  }

  public static function success($params, $source) {
    if(!(isset($params['text'], $params['type']) && $params['text'] && $params['type'] && $source))
      return false;

    $action = json_decode($source->action, true);
    if(!(isset($action['kind'], $action['currencyId'], $action['bankId'])))
      return false;

    if(!$currency = \M\Currency::one('id = ?', $action['currencyId']))
      return false;
    if($action['bankId'] != 0 && !$bank = \M\Bank::one('id = ?', $action['bankId']))
      return false;

    if(!$obj = \M\RemindRange::create(array_merge($action, ['value' => $params['text'], 'type' => $params['type'], 'sourceId' => $source->id])))
      return false;

    ($source->action = '') && $source->save();

    return MyLineBotMsg::create()->flex('已設定成功', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create('已設定成功')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create([
              FlexText::create($currency->name . ($action['bankId'] != 0 ? ' / ' . $bank->name : ''))->setColor('#906768'),
              FlexSeparator::create(),

              FlexBox::create([
                FlexBox::create([
                  FlexText::create('內容')->setFlex(2),
                  FlexSeparator::create()->setMargin('md'),
                  FlexText::create(\M\RemindRange::KIND[$action['kind']] . ($params['type'] == \M\RemindRange::TYPE_LESS ? ' < = ' : ' > = ') . $params['text'])->setFlex(8)->setMargin('lg'),
                ])->setLayout('horizontal'),

                FlexSeparator::create()->setMargin('md'),

                FlexBox::create([
                  FlexText::create('日期')->setFlex(2),
                  FlexSeparator::create()->setMargin('md'),
                  FlexText::create((string)$obj->createAt)->setFlex(8)->setMargin('lg'),
                ])->setLayout('horizontal')->setMargin('md'),

              ])->setLayout('vertical')
              
            ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]));

  }
}

class RemindFloat{
  public static function create($params, $source) {
    if(!(isset($params['kind']) && $params['kind'] && $source))
      return false;
    if(\M\RemindFloat::count(['sourceId = ?', $source->id]) >= 2)
      return MyLineBotMsg::create()->text('已超過上限，最多只能設定2個浮動提醒！'); 

    return Common::currency(['lib' => 'postback/Richmenu', 'class' => 'RemindFloat', 'method' => 'bank', 'param' => ['kind' => $params['kind']]]);
  }

  public static function bank($params) {
    if(!(isset($params['currencyId'])))
      return false;
    return Common::bank($params['currencyId'], ['lib' => 'postback/Richmenu', 'class' => 'RemindFloat', 'method' => 'input', 'param' => ['currencyId' => $params['currencyId'], 'kind' => $params['kind']]], $params['kind']);
  }

  public static function input($params, $source) {
    ($source->action = json_encode(['class' => 'RemindFloat', 'method' => 'success', 'kind' => $params['kind'], 'currencyId' => $params['currencyId'], 'bankId' => $params['bankId']])) && $source->save();
    return MyLineBotMsg::create()->text('請輸入浮動值'); 
  }

  public static function delete($params, $source) {
    if(!(isset($params['id']) && $params['id'] && $source))
      return false;

    if($obj = \M\RemindFloat::one('id = ?', $params['id']))
      if($obj->delete())
        return Remind::show($params, $source);
  }

  public static function success($text, $source) {
    if(!($text && $source))
      return false;

    $action = json_decode($source->action, true);
    if(!(isset($action['kind'], $action['currencyId'], $action['bankId'])))
      return false;

    if(\M\RemindFloat::one('sourceId = ? and bankId = ? and value = ? and kind = ?', $source->id, $action['bankId'] ? $action['bankId'] : 0, $text, $action['kind']))
      return MyLineBotMsg::create()->text('此區間範圍已設定過，請重新設定'); 

    if(!$currency = \M\Currency::one('id = ?', $action['currencyId']))
      return false;
    if(!$bank = \M\Bank::one('id = ?', $action['bankId']))
      return false;

    if(!$obj = \M\RemindFloat::create(array_merge($action, ['value' => $text, 'sourceId' => $source->id, 'now' => 0])))
      return false;

    ($source->action = '') && $source->save();

    return MyLineBotMsg::create()->flex('已設定成功', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create('已設定成功')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create([
              FlexText::create($currency->name . ' / ' . $bank->name)->setColor('#906768'),
              FlexSeparator::create(),

              FlexBox::create([
                FlexBox::create([
                  FlexText::create('內容')->setFlex(2),
                  FlexSeparator::create()->setMargin('md'),
                  FlexText::create(\M\RemindRange::KIND[$action['kind']] . ' +- ' . $text)->setFlex(8)->setMargin('lg'),
                ])->setLayout('horizontal'),

                FlexSeparator::create()->setMargin('md'),

                FlexBox::create([
                  FlexText::create('日期')->setFlex(2),
                  FlexSeparator::create()->setMargin('md'),
                  FlexText::create((string)$obj->createAt)->setFlex(8)->setMargin('lg'),
                ])->setLayout('horizontal')->setMargin('md'),

              ])->setLayout('vertical')
              
            ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]));
  }
}



