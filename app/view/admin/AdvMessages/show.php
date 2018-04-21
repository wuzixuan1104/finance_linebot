<div class='back'>
  <a href="<?php echo RestfulUrl::index ();?>" class='icon-36'>回上一頁</a>
</div>

<div class='show-panel'>
<?php echo $show->appendRows(
  Restful\ShowText::create('廣告標題', function ($obj) {
    return $obj->adv->title;
  }),

  Restful\ShowText::create('會員', function ($obj) {
    return $obj->user->name . ' (' . $obj->user->email . ')';
  }),

  Restful\ShowPure::create('內容', function($obj) {
    return $obj->content;
  }),

  Restful\ShowDate::create('建立日期', function ($obj) {
    return $obj->created_at;
  })
); ?>

</div>
