<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Line extends ApiController {

  public function __construct() {
    parent::__construct();
  }

  public function index() {
    Load::lib ('MyLineBot.php');
    $events = MyLineBot::events();

    foreach( $events as $event ) {
      // switch($event->getMessageType()) {
      //   case "text":
      //     $msg = MyLineBotMsg::create ()
      //                 ->multi ([
      //                   MyLineBotMsg::create ()->text ($event->getText()),
      //                   MyLineBotMsg::create ()->text ('hello')
      //                 ])
      //                 ->reply ($event->getReplyToken());
      //     break;
      //   case "image":
          // $url = 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg';
          // $msg = MyLineBotMsg::create ()
          //             ->multi([
          //               MyLineBotMsg::create ()->image($url, $url),
          //             ])
          //             ->reply ($event->getReplyToken());
          // case "video":
            // $url = 'https://youtu.be/n3GhjRiVtns';
            // $msg = MyLineBotMsg::create()->video($url, $url)->reply ($event->getReplyToken());

            // break;
          // case "audio":
          //   $url =
          //   break;

          // break;
      // }

      // $msg = MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
      //           MyLineBotMsg::create()->templateButton("按鈕文字","說明", 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg', [
      //             MyLineBotActionMsg::create()->message("按鈕1","文字1"),
      //             MyLineBotActionMsg::create()->uri("Google","http://www.google.com"),
      //             MyLineBotActionMsg::create()->postback("下一頁", "page=3"),
      //             MyLineBotActionMsg::create()->postback("上一頁", "page=1"),
      //           ])
      //        )->reply ($event->getReplyToken());

      Log::info('====================');
      $builder = new \LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder('https://angel.ioa.tw/res/image/t5', 'test',
                    new \LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder(1040,1040),
                    [ new \LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder('文字', new \LINE\LINEBot\ImagemapActionBuilder\AreaBuilder(0,0,100,100) ) ]
                 );
      // print_r($builder);
      // die;
      Log::info($event->getReplyToken());

      MyLineBot::bot()->replyMessage($event->getReplyToken(), $builder);
      Log::info('====================');

    }

  }

}
