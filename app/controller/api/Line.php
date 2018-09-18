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
          // $a = MyLineBotMsg::create()->flex('匯率提醒', FlexBubble::create([
          //   'header' => FlexBox::create([FlexText::create('匯率提醒')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
          //   'body' => FlexBox::create([
          //     FlexText::create('設定提醒')->setColor('#906768'),
          //     FlexSeparator::create(),
          //     FlexBox::create([
          //       FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('區間', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'show', 'param' => []]), '區間')),
          //       FlexSeparator::create()->setMargin('lg'),
          //       FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setMargin('lg')->setAction(FlexAction::postback('浮動', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'show', 'param' => []]), '浮動')),
          //     ])->setLayout('horizontal'),
          //     FlexSeparator::create(),
          //     FlexText::create('已設定的提醒')->setColor('#906768'),
          //     FlexSeparator::create(),
          //     FlexButton::create('primary')->setColor('#f9b071')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('前往查看', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'show', 'param' => []]), '前往查看')),
          //     FlexSeparator::create(),
          //     FlexButton::create('primary')->setColor('#bbbec3')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('使用說明', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'show', 'param' => []]), '使用說明')),
          //   ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
          //   'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          // ]))->reply($event->getReplyToken());


          // die;



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