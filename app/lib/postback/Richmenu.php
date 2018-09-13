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
                      FlexButton::create('primary')->setColor('#db6a69')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('選擇', null, json_encode(['lib' => 'postback/RichMenu', 'class' => 'Search', 'method' => 'getCurrency', 'param' => ['currencyId' => $v->id]])))
                    ])->setLayout('horizontal')->setSpacing('md');

        $flexes[] = FlexSeparator::create();
      }

      $bubbles[] = FlexBubble::create([
                    'header' => FlexBox::create([FlexText::create('選擇貨幣')->setWeight('bold')->setSize('lg')->setColor('#E9ECEF')])->setSpacing('xs')->setLayout('horizontal'),
                    'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                    'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#6f6b6b'))
                  ]);
      $flexes = [];
    }

    return MyLineBotMsg::create()->flex('貨幣類別', FlexCarousel::create($bubbles)); 
  }

  public static function getCurrency() {

  }
}

class Calculate {
   public static function create() {

  }
}

class History {

}