<div class='back'>
  <a href="<?php echo RestfulUrl::index ();?>" class='icon-36'>回上一頁</a>
</div>

<div class='show-panel'>
<?php echo $show->appendRows(
  Restful\ShowText::create('商品ID', function ($obj) {
    return $obj->id;
  }),

  Restful\ShowText::create('品牌名稱', function ($obj) {
    return $obj->brand->name;
  }),

  Restful\ShowText::create('商品名稱', function ($obj) {
    return $obj->name;
  }),

  Restful\ShowPure::create('拍攝規則', function ($obj) {
    return $obj->rule;
  }),

  Restful\ShowPure::create('簡述', function($obj) {
    return $obj->description;
  }),

  Restful\ShowText::create('拍攝次數', function($obj) {
    return $obj->cnt_shoot;
  }),

  Restful\ShowDate::create('建立日期', function ($obj) {
    return $obj->created_at;
  }),

  Restful\ShowImages::create('圖片', function($obj) {
    return $obj->pic->url();
  })->setItem( $obj->details )
); ?>

<?php
  if( $details = $obj->details )
    echo '<div class="pure"><b>影片</b>' . implode('', array_map( function($detail) {
      return ($detail->file != '') ? '<video width="320" height="240" controls>' .  '<source src="' . $detail->file->url() . '" type="video/mp4"></source></video>' : '';
    }, $details) ) . '</div>';
?>
</div>
