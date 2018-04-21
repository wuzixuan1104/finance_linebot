<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class AdvDetail extends Model {
  static $table_name = 'adv_details';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const TYPE_PICTURE = 'picture';
  const TYPE_YOUTUBE = 'youtube';
  const TYPE_VIDEO = 'video';

  static $typeTexts = array (
    self::TYPE_PICTURE  => '圖片',
    self::TYPE_YOUTUBE  => 'Youtube',
    self::TYPE_VIDEO  => '影片',
  );

  public function __construct ($attrs = array (), $guardAttrs = true, $instantiatingViafind = false, $newRecord = true) {
    parent::__construct ($attrs, $guardAttrs, $instantiatingViafind, $newRecord);

    // 設定圖片上傳器
    Uploader::bind ('pic', 'AdvDetailPicImageUploader');

    // 設定檔案上傳器
    Uploader::bind ('file', 'AdvDetailFileFileUploader');
  }

  public function destroy () {
    if (!isset ($this->id))
      return false;

    return $this->delete ();
  }

  public function putFiles ($files) {
    foreach ($files as $key => $file)
      if (isset ($files[$key]) && $files[$key] && isset ($this->$key) && $this->$key instanceof Uploader && !$this->$key->put ($files[$key]))
        return false;
    return true;
  }
}

/* -- 圖片上傳器物件 ------------------------------------------------------------------ */
class AdvDetailPicImageUploader extends ImageUploader {
  public function getVersions () {
    return array (
        '' => array (),
        'w100' => array ('resize', 100, 100, 'width'),
        'c1200x630' => array ('adaptiveResizeQuadrant', 1200, 630, 't'),
      );
  }
}

/* -- 檔案上傳器物件 ------------------------------------------------------------------ */
class AdvDetailFileFileUploader extends FileUploader {
}
