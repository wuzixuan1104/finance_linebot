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

          

          

          //選擇範圍
          $a = MyLineBotMsg::create()->flex('選擇範圍區間', FlexBubble::create([
            'header' => FlexBox::create([FlexText::create('選擇範圍區間')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
            'body' => FlexBox::create([
              FlexText::create('當匯率符合所選範圍時會發出通知')->setColor('#906768'),
              FlexSeparator::create(),
              FlexButton::create('primary')->setColor('#f9b071')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('> = 30.123', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'show', 'param' => []]), '大於等於')),
              FlexButton::create('primary')->setColor('#f37370')->setFlex(3)->setHeight('sm')->setGravity('center')->setAction(FlexAction::postback('< = 30.123', json_encode(['lib' => 'postback/Richmenu', 'class' => 'Search', 'method' => 'show', 'param' => []]), '小於等於')),
              FlexText::create('ps. 一天至多提醒一次')->setColor('#a5a3a3')->setSize('sm'),
            ])->setLayout('vertical')->setSpacing('md')->setMargin('sm'),
            'styles' => FlexStyles::create()->setHeader(FlexBlock::create()->setBackgroundColor('#f7d8d9'))
          ]))->reply($event->getReplyToken());

          //區間設定成功
          // $a = MyLineBotMsg::create()->flex('區間已設定', FlexBubble::create([
          //   'header' => FlexBox::create([FlexText::create('區間已設定')->setWeight('bold')->setSize('lg')->setColor('#904d4d')])->setSpacing('xs')->setLayout('horizontal'),
          //   'body' => FlexBox::create([
          //     FlexText::create('美國(美金) / 國泰世華')->setColor('#906768'),
          //     FlexSeparator::create(),

          //     FlexBox::create([
          //       FlexBox::create([
          //         FlexText::create('內容')->setFlex(2),
          //         FlexSeparator::create()->setMargin('md'),
          //         FlexText::create('牌告 > = 30.123')->setFlex(8)->setMargin('lg'),
          //       ])->setLayout('horizontal'),

          //       FlexSeparator::create()->setMargin('md'),

          //       FlexBox::create([
          //         FlexText::create('日期')->setFlex(2),
          //         FlexSeparator::create()->setMargin('md'),
          //         FlexText::create('2018-10-10 11:12:12')->setFlex(8)->setMargin('lg'),
          //       ])->setLayout('horizontal')->setMargin('md'),

          //     ])->setLayout('vertical')
              
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