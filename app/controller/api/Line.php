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

          // $a = MyLineBotMsg::create()->flex('匯率試算', FlexBubble::create([
          //         'header' => FlexBox::create([FlexText::create('匯率試算')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
          //         'body' => FlexBox::create([
          //            FlexBox::create([
          //               FlexBox::create([
          //                 FlexText::create('墨西哥披索')->setColor('#906768')->setSize('md'),
          //                 FlexText::create('/ 國泰銀行')->setSize('md'),
          //               ])->setLayout('vertical')->setFlex(4),

          //               FlexSeparator::create(),

          //               FlexButton::create('primary')->setFlex(3)->setColor('#d4d4d4')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback( '移除', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'input', 'param' => ['calc' => 'A']]), '移除')),
          //               FlexButton::create('primary')->setFlex(3)->setColor('#f37370')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback( '移除', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'input', 'param' => ['calc' => 'A']]), '移除')),

          //           ])->setLayout('horizontal')->setSpacing('md')->setMargin('lg'),
          //           FlexSeparator::create(),

          //           FlexBox::create([
          //               FlexBox::create([
          //                 FlexText::create('墨西哥披索')->setColor('#906768')->setSize('md'),
          //                 FlexText::create('/ 國泰銀行')->setSize('md'),
          //               ])->setLayout('vertical')->setFlex(4),

          //               FlexSeparator::create(),

          //               FlexButton::create('primary')->setFlex(3)->setColor('#d4d4d4')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback( '移除', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'input', 'param' => ['calc' => 'A']]), '移除')),
          //               FlexButton::create('primary')->setFlex(3)->setColor('#f37370')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback( '移除', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'input', 'param' => ['calc' => 'A']]), '移除')),

          //           ])->setLayout('horizontal')->setSpacing('md')->setMargin('lg'),
          //           FlexSeparator::create()

          //         ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
          //           'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          //       ]))->reply($event->getReplyToken());

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