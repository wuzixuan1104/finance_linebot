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
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;


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

class MyLineBotMsg {
  private $builder;

  public function __construct() {
  }

  public static function create() {
    return new MyLineBotMsg();
  }
  public function reply ($token) {
    if ($this->builder)
      MyLineBot::bot()->replyMessage($token, $this->builder);
  }
  public function getBuilder() {
    return $this->builder;
  }
  public function text($text) {
    $this->builder = !is_null($text) ? new TextMessageBuilder($text) : null;
    return $this;
  }
  public function image($url1, $url2) {
    $this->builder = is_string ($url1) && is_string ($url2) ? new ImageMessageBuilder($url1, $url2) : null;
    return $this;
  }
  public function sticker($packId, $id) {
    $this->builder = is_numeric($packId) && is_numeric($id) ? new StickerMessageBuilder($packId, $id) : null;
    return $this;
  }
  public function video($ori, $prev) {
    $this->builder = isHttps($ori) && isHttps($prev) ? new VideoMessageBuilder($ori, $prev) : null;
    return $this;
  }
  public function audio($ori, $d) {
    $this->builder = isHttps($ori) && is_numeric($d) ? new VideoMessageBuilder($ori, $d) : null;
    return $this;
  }
  public function location($title, $add, $lat, $lon) {
    $this->builder = is_string($title) && is_string($add) && is_numeric($lat) && is_numeric($lon) ? new LocationMessageBuilder($ori, $d) : null;
    return $this;
  }
  public function imagemap($url, $altText, $baseSizeBuilder, array $actionBuilders) {
    $this->builder = isHttps($url) && is_string($altText) && is_string($add) && is_numeric($lat) && is_numeric($lon) ? new LocationMessageBuilder($ori, $d) : null;
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
  public function templateButton($title, $text, $imageUrl, array $actionBuilders) {
    $this->builder = is_string($title) && is_string($text) && is_string($imageUrl) && is_array($actionBuilders) ? new ButtonTemplateBuilder($title, $text, $imageUrl, $actionBuilders) : null;
    return $this;
  }
  public function templateCarousel() {

  }
  public function template($text, $builder) {
    if( !is_string($text) || empty($builder) )
      return $this;

    $this->builder = new TemplateMessageBuilder($text, $builder->getBuilder());
    return $this;
  }


}

class MyLineBotActionMsg {
  private $action;
  public function __construct() {

  }
  public static function create() {
    return new MyLineBotActionMsg();
  }
  public function message($text1, $text2) {
    return is_string($text1) && is_string($text2) ? new MessageTemplateActionBuilder($text1, $text2) : null;
  }
  public function uri($text, $url) {
    return is_string($text) && (isHttp($url) || isHttps($url)) ? new UriTemplateActionBuilder($text, $url) : null;
  }
  public function postback($text, $href) {
    return is_string($text) && is_string($href) ? new PostbackTemplateActionBuilder($text, $href) : null;
  }
}
