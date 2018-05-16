<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Text extends Model {
  static $table_name = 'texts';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attrs = array (), $guardAttrs = true, $instantiatingViafind = false, $newRecord = true) {
    parent::__construct ($attrs, $guardAttrs, $instantiatingViafind, $newRecord);
  }

  public function destroy () {
    if (!isset ($this->id))
      return false;

    return $this->delete ();
  }

  public static function save($source, $speaker, $event) {
    if( !$event->getText() )
      return false;

    $param = array(
      'source_id' => $source->sid,
      'speaker_id' => $speaker->sid,
      'reply_token' => $event->getReplyToken(),
      'message_id' => $event->getMessageId(),
      'text' => $event->getText(),
    );

    if( !$obj = Text::create( $param ) )
      return false;
    return $obj;
  }
}
