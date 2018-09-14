<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Load::lib('MyLineBot.php');

class Line extends Controller {
  static $cache;

  public function index() {
    $events = MyLineBot::events();

    foreach( $events as $event ) {
      if( !$source = \M\Source::checkExist($event) )
        continue;

      if (!$log = MyLineBotLog::init($source, $event)->create())
        return false;

      switch( trim(get_class($log), "M\\") ) {
        case 'Join':
          break;
        case 'Leave':
          break;
        case 'Follow':
          break;
        case 'Unfollow':
          break;
        case 'Text':
          $pattern = 'hello';
          $pattern = !preg_match ('/\(\?P<k>.+\)/', $pattern) ? '/(?P<k>(' . $pattern . '))/i' : ('/(' . $pattern . ')/i');
          preg_match_all ($pattern, $log->text, $result);

          $a = MyLineBotMsg::create()->flex('問題類別', FlexBubble::create([
                  'header' => FlexBox::create([FlexText::create('中國(離岸人民幣) / 第一銀行')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                  'body' => FlexBox::create([
                      FlexBox::create([
                        FlexText::create('牌告匯率')->setColor('#906768'),
                        FlexText::create('2018-08-08')->setSize('xxs')->setAlign('end')->setColor('#bbbbbb')
                      ])->setLayout('horizontal'),

                      FlexSeparator::create(),

                      FlexBox::create([
                        FlexBox::create([
                          FlexBox::create([FlexText::create('賣出：30.512')])->setLayout('vertical')
                        ])->setLayout('vertical')->setFlex(5),
                        
                        FlexSeparator::create(),

                        FlexBox::create([
                          FlexBox::create([FlexText::create('賣出：30.512')])->setLayout('vertical')
                        ])->setLayout('vertical')->setFlex(5)
                      ])->setLayout('horizontal')->setSpacing('md'),




                      FlexSeparator::create(),




                      FlexBox::create([
                        FlexText::create('現鈔匯率')->setColor('#906768'),
                        FlexText::create('2018-08-08')->setSize('xxs')->setAlign('end')->setColor('#bbbbbb')
                      ])->setLayout('horizontal'),

                      FlexSeparator::create(),

                      FlexBox::create([
                        FlexBox::create([
                          FlexBox::create([FlexText::create('賣出：30.512')])->setLayout('vertical')
                        ])->setLayout('vertical')->setFlex(5),
                        
                        FlexSeparator::create(),

                        FlexBox::create([
                          FlexBox::create([FlexText::create('賣出：30.512')])->setLayout('vertical')
                        ])->setLayout('vertical')->setFlex(5)
                      ])->setLayout('horizontal')->setSpacing('md'),

                    ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                  'footer' => FlexBox::create([
                    FlexButton::create('primary')->setColor('#f97172')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('匯率試算', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'type', 'param' => ['currencyId' => '', 'bankId' => '']]), null))
                  ])->setLayout('horizontal')->setSpacing('xs'),
                  'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                ]))->reply($event->getReplyToken());
          // MyLineBotMsg::create()
          //   ->text($log->text)
          //   ->reply($event->getReplyToken());

          // if ($result['k'] && $msg = ForexProcess::begin() )
          //   $msg->reply($event->getReplyToken());

          break;
        case 'Postback':
          Log::info(0);
          $data = json_decode( $log->data, true );
          Log::info(1);
          if( !( isset( $data['lib'], $data['class'], $data['method'] ) && ( isset( self::$cache['lib'][$data['lib']] ) ? true : ( Load::lib($data['lib'] . '.php') ? self::$cache['lib'][$data['lib']] = true : false ) )
            && method_exists($class = $data['class'], $method = $data['method']) && $msg = $class::$method( $data['param'], $source ) ) )
          return false;
          Log::info(2);
          $msg->reply($event->getReplyToken());
          break;
      }
    }
  }
}