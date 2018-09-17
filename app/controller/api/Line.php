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
          // $pattern = 'hello';
          // $pattern = !preg_match ('/\(\?P<k>.+\)/', $pattern) ? '/(?P<k>(' . $pattern . '))/i' : ('/(' . $pattern . ')/i');
          // preg_match_all ($pattern, $log->text, $result);

          if(is_numeric($log->text) && $source->action) {
            if( isset( self::$cache['lib']['postback/Richmenu'] ) ? true : ( Load::lib('postback/Richmenu.php') ? self::$cache['lib']['postback/Richmenu'] = true : false ) ) 
              $msg = Calculate::show($log->text, $source);
              $msg->reply($event->getReplyToken());
          }

          die;
          $a = MyLineBotMsg::create()->flex('試算模式', FlexBubble::create([
                  'header' => FlexBox::create([FlexText::create('選擇試算模式')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
                  'body' => FlexBox::create([
                     FlexText::create('輸入金額：1500')->setColor('#906768'),
                     FlexSeparator::create(),

                     FlexBox::create([

                        FlexBox::create([

                          FlexBox::create([
                            FlexText::create('牌告')->setFlex(3),
                            FlexSeparator::create(),
                            FlexText::create('31.035')->setMargin('lg')->setFlex(7)
                          ])->setLayout('horizontal')->setSpacing('md'),

                          FlexSeparator::create()->setMargin('md'),

                          FlexBox::create([
                            FlexText::create('現鈔')->setFlex(3),
                            FlexSeparator::create(),
                            FlexText::create('31.035')->setMargin('lg')->setFlex(7)
                          ])->setLayout('horizontal')->setSpacing('md')->setMargin("md"),

                          FlexSeparator::create()->setMargin('md'),
                          FlexText::create('ps. 可直接輸入金額再重新試算')->setSize('xs')->setMargin('lg')->setColor('#969696')
                        
                          

                        ])->setLayout('vertical'),
                        

                      ])->setLayout('horizontal')->setSpacing('md')

                  ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
                  'footer' => FlexBox::create([FlexButton::create('primary')->setColor('#f97172')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('重新選擇試算方式', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'type', 'param' => []]), null))])->setLayout('horizontal')->setSpacing('xs'),
                  'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
                ]))->reply($event->getReplyToken());

          // MyLineBotMsg::create()
          //   ->text($log->text)
          //   ->reply($event->getReplyToken());

          // if ($result['k'] && $msg = ForexProcess::begin() )
          //   $msg->reply($event->getReplyToken());

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