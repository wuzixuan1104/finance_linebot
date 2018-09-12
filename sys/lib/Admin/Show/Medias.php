<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminShowMedias extends AdminShowUnitNomin {
  public function content($medias) {
    $medias = implode('', array_map(function($media) {
      
      if (is_array($media))
        if ($media['type'] == 'video')
          return '<div class="video">' . (($media['video'] = $media['video'] instanceof \M\FileUploader ? $media['video']->url() : $media['video']) ? '<video controls src="' . $media['video'] . '">你的瀏覽器可能無法播放喔！</video>' : '') . '</div>';
        else
          return '<div class="_ic image">' . (($media['img'] = $media['img'] instanceof \M\ImageUploader ? $media['img']->url() : $media['img']) ? '<img src="' . $media['img'] . '"><div class="icon-13"></div>' : '') . '</div>';
      else
        return '<div class="_ic image">' . (($media = $media instanceof \M\ImageUploader ? $media->url() : $media) ? '<img src="' . $media . '"><div class="icon-13"></div>' : '') . '</div>';
    }, $medias));

    parent::content($medias);

    return $this->className('medias');
  }
}