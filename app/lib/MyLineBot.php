<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OC Wu <cherry51120@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

use LINE\LINEBot;
use LINE\LINEBot\Constant;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class MyLineBot {
  private $bot = null;
  public function __construct($bot) {
    $this->bot = $bot;
  }
  public function getBot() { return $this->bot; }
  public static function create() {
    $mybot = new LINEBot( new CurlHTTPClient(config('line', 'channelToken')), ['channelSecret' => config('line', 'channelSecret')]);
    return new MyLineBot($mybot);
  }
  public static function events() {
    if( !isset ($_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE]) )
      return false;
    try {
      return MyLineBot::create()->getBot()->parseEventRequest (file_get_contents ("php://input"), $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE]);
    } catch (Exception $e) {
      return $e;
    }
  }
}

class MyLineBotMsg {
  private $builder;
  public function __construct() {
  }
  public static function create() {
    return new MyLineBotMsg();
  }
  public function getBuilder() {
    return $this->builder;
  }
  public function _text($text) {
    $this->builder = ( isset($text) && $text != '' ) ? new TextMessageBuilder($text) : '';
    return $this;
  }
  public function _image() {

  }
  public function __call($name, $args) {
    method_exists($this, '_' . $name) || Validation::error('錯誤');
    call_user_func_array (array ($this, '_' . $name), $args);
    return $this->getBuilder();
  }
}

class MyLineBotMultiMsg {
  private $multiBuilder;
  public function __construct($multiBuilder) {
    $this->multiBuilder = $multiBuilder;
  }
  public static function create() {
    return new MyLineBotMultiMsg( new MultiMessageBuilder() );
  }
  public function getMultiBuilder() {
    return $this->multiBuilder;
  }
}
