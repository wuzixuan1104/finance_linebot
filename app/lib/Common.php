<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Common {
  public static function currency($params) {
    if(!$currencies = \M\Currency::all(['where' => ['enable = ?', \M\Currency::ENABLE_ON]]) )
      return false;

    $currencies = array_chunk($currencies, 5);

    $flexes = [];
    $bubbles = [];
    foreach($currencies as $currency) {
      foreach($currency as $v) {
        $params['param']['currencyId'] = $v->id;
        
        $flexes[] = FlexBox::create([
                      FlexBox::create([FlexText::create($v->name)])->setLayout('vertical')->setFlex(7),
                      FlexSeparator::create(),
                      FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('選擇', json_encode($params), $v->name))
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

  public static function currencyType($currencyId, $assign) {
    $records = [];
    if($assign == 'all' || $assign == 'passbook') 
      if($passbooks = \M\PassbookRecord::all(['where' => ["( bankId, currencyId, createAt ) in ( select `bankId`, `currencyId`, max(`createAt`) from `PassbookRecord` where `currencyId` = ? group by `bankId` ) ", $currencyId] ]))
        array_map( function($v) use(&$records) { return $records[$v->bankId] = $v->bank->name; }, $passbooks);
    
    if($assign == 'all' || $assign == 'cash') 
      if($cashes = \M\CashRecord::all(['where' => ["( bankId, currencyId, createAt ) in ( select `bankId`, `currencyId`, max(`createAt`) from `CashRecord` where `currencyId` = ? group by `bankId` ) ", $currencyId] ]))
        array_map( function($v) use(&$records) { return $records[$v->bankId] = $v->bank->name; }, $cashes);
    
    if(!$records)
      return false;

    return $records;
  }

  public static function bank($currencyId, $params, $assign = 'all') {
    if(!(isset($currencyId) && $params))
      return false;

    if(!$records = self::currencyType($currencyId, $assign))
      return false;

    $flexes = $bubbles = [];
    $cnt = 0;
    foreach($records as $k => $v) {
      $params['param']['bankId'] = $k;
      
      $flexes[] = FlexBox::create([
                    FlexBox::create([FlexText::create($v)])->setLayout('vertical')->setFlex(7),
                    FlexSeparator::create(),
                    FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('選擇', json_encode($params), $v))
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
}