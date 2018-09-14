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

          // $a = MyLineBotMsg::create()->flex('試算模式', FlexBubble::create([
          //         'header' => FlexBox::create([FlexText::create('選擇試算模式')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
          //         'body' => FlexBox::create([
          //             FlexButton::create('primary')->setColor('#f1c87f')->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('墨西哥(墨西哥披索) -> 台幣', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'type', 'param' => ['currencyId' => '', 'bankId' => '']]), '墨西哥(墨西哥披索) -> 台幣')),
          //             FlexButton::create('primary')->setColor('#f97172')->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('台幣 -> 墨西哥(墨西哥披索)', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Calculate', 'method' => 'type', 'param' => ['currencyId' => '', 'bankId' => '']]), '墨西哥(墨西哥披索) -> 台幣')),

          //         ])->setLayout('vertical'),
          //         'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          //       ]))->reply($event->getReplyToken());
          
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