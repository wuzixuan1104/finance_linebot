<div class='back'>
  <a href="<?php echo RestfulUrl::index ();?>" class='icon-36'>回上一頁</a>
</div>

<div class='show-panel'>
<?php echo $show->appendRows(
  Restful\ShowText::create('標題', function ($obj) {
    return $obj->title;
  }),

  Restful\ShowList::create('類型', function ($obj) {
     return $obj;
  })->setItem( [ Adv::$typeTexts[$obj->type] ]  ),

  Restful\ShowText::create('創建者', function ($obj) {
    return $obj->user->name . ' (' . $obj->user->email . ')';
  }),

  Restful\ShowText::create('審核', function ($obj) {
    return Adv::$reviewTexts[$obj->review];
  }),

  Restful\ShowPure::create('簡述', function($obj) {
    return $obj->description;
  }),

  Restful\ShowPure::create('內容', function($obj) {
    return $obj->content;
  }),

  Restful\ShowMulti::create('統計資料', function($detail) {
    return ['title' => $detail['title'], 'content' => $detail['content'] ];
  })->setItem( [ ['title' => '喜歡數', 'content' => $obj->cnt_like ],
                 ['title' => '留言數', 'content' => $obj->cnt_message ],
                 ['title' => '瀏覽數', 'content' => $obj->cnt_view ] ] ),


  Restful\ShowList::create('影片', function ($obj) {
     return $obj->file && $obj->type == AdvDetail::TYPE_VIDEO ? $obj->file->url() : '';
  })->setItem( $obj->details ),

  Restful\ShowImages::create('圖片', function($obj) {
    return $obj->pic->url();
  })->setItem( $obj->details ),

  Restful\ShowDate::create('建立日期', function ($obj) {
    return $obj->created_at;
  })
); ?>

<?php
  if( $details = $obj->details )
    echo '<div>' . implode('', array_map( function($detail) {
      return ($detail->file != '') ? '<video width="320" height="240" controls>' .  '<source src="' . $detail->file->url() . '" type="video/mp4"></source></video>' : '';
    }, $details) ) . '</div>';
?>
</div>
