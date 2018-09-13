<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Load::lib('MyLineBot.php');

class Search {

  public static function create() {
    if(!$currencies = \M\Currency::all(['where' => ['enable = ?', \M\Currency::ENABLE_ON]]) )
      return false;

    $flexes = [];
    foreach($currencies as $currency) {
      $flexes[] = FlexBox::create([
                    FlexBox::create([FlexText::create($currency->name)])->setLayout('vertical')->setFlex(7),
                    FlexSeparator::create(),
                    FlexButton::create('primary')->setColor('#E6AE5F')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('選擇', null, json_encode(['lib' => 'postback/RichMenu', 'class' => 'Search', 'method' => 'getCurrency', 'param' => ['currencyId' => $currency->id]])))
                  ])->setLayout('horizontal')->setSpacing('md');
      $flexes[] = FlexSeparator::create();

    }

    return MyLineBotMsg::create()->flex('貨幣類別', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create('選擇貨幣')->setWeight('bold')->setSize('lg')->setColor('#E9ECEF')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create($flexes)->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#3A5762'))
          ]));
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