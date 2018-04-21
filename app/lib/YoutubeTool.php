<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class YoutubeTool {
  public static function biggerYoutubeImageUrl ($vid) {
    if (!$urls = self::youtubeImageUrls ($vid))
      return '';

    $url = $urls[0]['url'];
    
    if (!$urls = array_filter ($urls, function ($image) { return isset ($image['width']) && isset ($image['height']); }))
      return $url;
    
    usort ($urls, function ($a, $b) { return $a['width'] * $a['height'] < $b['width'] * $b['height']; });
    
    $url = array_shift ($urls);

    return $url['url'];
  }
  public static function youtubeImageUrls ($vid) {
    $info = self::youtubeInfo ($vid);
    return isset ($info['thumbnails']) && ($thumbnails = $info['thumbnails']) ? $info['thumbnails'] : array ();
  }
  public static function youtubeInfo ($vid) {
    Load::lib ('Google/Google.php', true);

    $client = new Google_Client ();
    $client->setDeveloperKey (config ('google', 'key'));
    $youtube = new Google_Service_YouTube ($client);

    try {
      $response = $youtube->videos->listVideos ('id, snippet', array ('id' => $vid));
      return isset ($response->items[0]) ? self::googleSearchResultSnippetFormat ($response->items[0]) : array ();
    } catch (Exception $e) {
      return array ();
    }
  }
  public static function googleSearchResultSnippetFormat ($item) {
    $sizes = array ('getDefault', 'getHigh', 'getMaxres', 'getMedium', 'getStandard');
    $id = is_a ($item, 'Google_Service_YouTube_SearchResult') ? $item->id->videoId : (is_a ($item, 'Google_Service_YouTube_Video') ? $item->id : '');

    return $id && isset ($item->snippet) ? array (
          'id' => $id,
          'content' => isset ($item->snippet->content) ? $item->snippet->content : '',
          'title' => isset ($item->snippet->title) ? $item->snippet->title : '',
          'tags' => isset ($item->snippet->tags) ? $item->snippet->tags : array (),
          'publishedAt' => isset ($item->snippet->publishedAt) ? $item->snippet->publishedAt : '',
          'thumbnails' => isset ($item->snippet->thumbnails) ? array_filter (array_map (function ($size) use ($item) {
              if (!method_exists ($item->snippet->thumbnails, $size))
                return null;
              
              $thumbnail = call_user_func_array (array ($item->snippet->thumbnails, $size), array ());
              
              if (!isset ($thumbnail->url))
                return null;

              return array_merge (array ('url' => $thumbnail->url), isset ($thumbnail->width) && isset ($thumbnail->height) ? array ('width' => $thumbnail->width, 'height' => $thumbnail->height) : array ());
            }, $sizes)) : array (),
        ) : array ();
  }
}