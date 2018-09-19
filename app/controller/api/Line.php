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


          // 提醒
          $bubbles = [];
          for($i = 0; $i < 2; $i++ ) {
          $bubbles[] =  FlexBubble::create([
            'header' => FlexBox::create([FlexText::create('匯率提醒列表')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create([
                FlexText::create('-- 區間設定 --')->setColor('#e46767')->setSize('md'),
                FlexSeparator::create(),
                FlexBox::create([
                  FlexBox::create([
                    FlexText::create('美國(美金) / 國泰銀行')->setColor('#906768')->setFlex(7),
                    FlexButton::create('primary')->setFlex(3)->setColor('#f97172')->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('移除', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'input', 'param' => []]), '移除')),
                  ])->setLayout('horizontal'),
                  FlexText::create('+- 0.05')->setFlex(7)
                ])->setLayout('vertical'),

                FlexSeparator::create(),
                FlexText::create('2018-09-19 11:12:23')->setSize('xs')->setAlign('end')->setColor('#bdbdbd'),
                FlexSeparator::create(),

                FlexBox::create([
                  FlexBox::create([
                    FlexText::create('美國(美金)')->setColor('#906768')->setFlex(7),
                    FlexButton::create('primary')->setFlex(3)->setColor('#f97172')->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('移除', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'input', 'param' => []]), '移除')),
                  ])->setLayout('horizontal'),
                  FlexText::create('牌告 >= 30.15')->setFlex(7)
                ])->setLayout('vertical'),

                FlexSeparator::create(),
                FlexText::create('2018-09-19 11:12:23')->setSize('xs')->setAlign('end')->setColor('#bdbdbd'),
                FlexSeparator::create(),

            ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]);
          }

          MyLineBotMsg::create()->flex('選擇銀行', FlexCarousel::create($bubbles))->reply($event->getReplyToken());

  
          die;



          if(is_numeric($log->text) && $source->action) {
            if( isset( self::$cache['lib']['postback/Richmenu'] ) ? true : ( Load::lib('postback/Richmenu.php') ? self::$cache['lib']['postback/Richmenu'] = true : false ) ) 
              $msg = Calculate::show($log->text, $source);
              $msg->reply($event->getReplyToken());
          }
          break;
        case 'Postback':
          $data = json_decode( $log->data, true );
          if( !( isset( $data['lib'], $data['class'], $data['method'] ) && ( isset( self::$cache['lib'][$data['lib']] ) ? true : ( Load::lib($data['lib'] . '.php') ? self::$cache['lib'][$data['lib']] = true : false ) )
            && method_exists($class = $data['class'], $method = $data['method']) && $msg = $class::$method( $data['param'], $source ) ) )
            return false;

          $msg->reply($event->getReplyToken());
          break;
      }
    }
  }
}