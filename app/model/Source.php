<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

\Load::lib ('MyLineBot.php');

class Source extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  public static function getTitle($event) {
    $response = \MyLineBot::bot()->getProfile($event->getUserId());
    Log::info($response);
    
    if ($response->isSucceeded() && $profile = $response->getJSONDecodedBody())
      return $profile['displayName'];
    return '無名人氏';
  }

  public static function checkExist($event) {
    if( !$sid = $event->getEventSourceId() )
      return false;

    if(!$obj = Source::one(['where' => ['sid = ?', $sid]])) {
      $params = ['sid' => $sid, 'title' => Source::getTitle($event)];

      transaction(function() use (&$obj, $params){
        return $obj = Source::create($params);
      });

      if(!RichMenuGenerator::create4user($sid))
        Log::error('建立richmenu失敗:' . $sid);
    }
    return $obj;
  }
}
