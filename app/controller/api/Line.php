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
      $builder = new \LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder('https://www.google.com.tw/maps/place/%E5%8F%B0%E7%81%A3/@23.69781,120.960515,3a,75y,163.5h,90t/data=!3m8!1e1!3m6!1sAF1QipNOAVkOptvFv0G0h5sPnJLOFEmcRVpIYfR_U4gr!2e10!3e11!6shttps:%2F%2Flh5.googleusercontent.com%2Fp%2FAF1QipNOAVkOptvFv0G0h5sPnJLOFEmcRVpIYfR_U4gr%3Dw234-h106-k-no-pi-0-ya163.5-ro-0-fo100!7i5376!8i2688!4m5!3m4!1s0x346ef3065c07572f:0xe711f004bf9c5469!8m2!3d23.4506483!4d121.2615967?hl=zh-TW', 'test',
                    new \LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder(100,100),
                    [ new \LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder('文字', new \LINE\LINEBot\ImagemapActionBuilder\AreaBuilder(10,10,100,100) ) ]
                 );
      // print_r($builder);
      // die;
      Log::info($event->getReplyToken());

      MyLineBot::bot()->replyMessage($event->getReplyToken(), $builder);
      Log::info('====================');

    }

  }

}
