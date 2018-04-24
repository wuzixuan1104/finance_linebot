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

// bot
class MyLineBot extends LINEBot{
  static $bot;

  public function __construct ($client, $option) {
    parent::__construct ($client, $option);
  }
  public static function create() {
    return new LINEBot( new CurlHTTPClient(config('line', 'channelToken')), ['channelSecret' => config('line', 'channelSecret')]);
  }
  public static function bot() {
    if (self::$bot)
      return self::$bot;

    return self::$bot = self::create ();
  }
  public static function events() {
    if( !isset ($_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE]) )
      return false;

    try {
      return MyLineBot::bot()->parseEventRequest (file_get_contents ("php://input"), $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE]);
    } catch (Exception $e) {
      return $e;
    }
  }
}
// builder 總合體
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
  public function text($text) {
    $this->builder = is_string($text) ? new TextMessageBuilder($text) : null;
    return $this;
  }
  public function multi($builds) {
    if (!is_array ($builds))
      $this->builder = null;

    $this->builder = new MultiMessageBuilder();
    foreach ($builds as $build) {
      $this->builder->add ($build->getBuilder ());
    }

    return $this;
  }
  public function image($url1, $url2) {
    $this->builder = is_string ($url1) && is_string ($url2) ? new ImageMessageBuilder($url1, $url2) : null;
    return $this;
  }
  public function reply ($token) {
    if ($this->builder)
      MyLineBot::bot()->replyMessage($token, $this->builder);
  }
}
//
// class MyLineBotMultiMsg extends MultiMessageBuilder{
//   private $multiBuilder;
//   public function __construct() {
//
//   }
//   public static function create() {
//     return new MyLineBotMultiMsg();
//   }
//   // public function setMultiBuilder() {
//   //   $this->multiBuilder = new MultiMessageBuilder();
//   //   return $this;
//   // }
//   // public function getMultiBuilder() {
//   //   return $this->multiBuilder;
//   // }
// }
