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
        $params['param'] && $params['param'] = array_merge($params['param'], ['currencyId' => $v->id]);
        
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
}