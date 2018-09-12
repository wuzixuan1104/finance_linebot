<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Load::lib('MyLineBot.php');

class Line extends Controller {
  public function index() {
    $events = MyLineBot::events();
    foreach( $events as $event ) {

      if( !$source = \M\Source::checkExist($event) )
        continue;

      if (!$log = MyLineBotLog::init($source, $event)->create())
        return false;

      switch( get_class($log) ) {
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

          MyLineBotMsg::create()
            ->text($log->text)
            ->reply($event->getReplyToken());

          // if ($result['k'] && $msg = ForexProcess::begin() )
          //   $msg->reply($event->getReplyToken());

          break;
        case 'Postback':
          // $data = json_decode( $log->data, true );
          // if ( !( isset( $data['lib'], $data['method'] ) && ( isset( self::$cache['lib'][$data['lib']] ) ? true : ( Load::lib($data['lib'] . '.php') ? self::$cache['lib'][$data['lib']] = true : true ) )
          //      && method_exists($lib = $data['lib'], $method = $data['method']) && $msg = $lib::$method( $data['param'], $log ) ) )
          //   return false;

          // $msg->reply($event->getReplyToken());
          break;
      }
    }
  }
}