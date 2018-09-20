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
          if(is_numeric($log->text) && $source->action) {
            if( isset( self::$cache['lib']['postback/Richmenu'] ) ? true : ( Load::lib('postback/Richmenu.php') ? self::$cache['lib']['postback/Richmenu'] = true : false ) ) 
              $msg = Calculate::show($log->text, $source);
              $msg->reply($event->getReplyToken());
          }
          break;
        case 'Postback':
          $data = json_decode( $log->data, true );
          if((isset( $data['lib'], $data['class'], $data['method'] ) && ( isset( self::$cache['lib'][$data['lib']] ) ? true : ( Load::lib($data['lib'] . '.php') ? self::$cache['lib'][$data['lib']] = true : false ) ))) {
            if(method_exists($class = $data['class'], $method = $data['method']) && $msg = $class::$method( $data['param'], $source ) ) {
              $msg->reply($event->getReplyToken());
            }
          }
           
          // if(!(isset( $data['lib'], $data['class'], $data['method'] ) && ( isset( self::$cache['lib'][$data['lib']] ) ? true : ( Load::lib($data['lib'] . '.php') ? self::$cache['lib'][$data['lib']] = true : false ) ))
          //   && method_exists($class = $data['class'], $method = $data['method']) && $msg = $class::$method( $data['param'], $source ) )
          //   return false;

          // $msg->reply($event->getReplyToken());
          break;
      }
    }
  }
}