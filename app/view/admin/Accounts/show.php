<div class='back'>
  <a href="<?php echo RestfulUrl::index ();?>" class='icon-36'>回上一頁</a>
</div>

<div class='show-panel'>
<?php echo $show->appendRows(
  Restful\ShowText::create('品牌名稱', function ($obj) {
    return $obj->name;
  }),

  Restful\ShowText::create('會員', function ($obj) {
    return $obj->user->name . ' (' . $obj->user->email . ')';
  }),

  Restful\ShowText::create('統一編號', function ($obj) {
    return $obj->tax_number;
  }),

  Restful\ShowText::create('信箱', function ($obj) {
    return $obj->email;
  }),

  Restful\ShowText::create('手機', function ($obj) {
    return $obj->phone;
  }),

  Restful\ShowText::create('網站', function ($obj) {
    return $obj->website;
  }),

  Restful\ShowPure::create('簡述', function($obj) {
    return $obj->description;
  }),

  Restful\ShowImages::create('圖片', function($obj) {
    return $obj->pic != '' ? '<img src="'. $obj->pic->url() . '"/>' : '';
  }),

  Restful\ShowMulti::create('公司資料', function($detail) {
    return ['title' => $detail['title'], 'content' => $detail['content'] ];
  })->setItem( [ ['title' => '名稱', 'content' => $obj->name ],
                 ['title' => '地址', 'content' => $obj->company_city . $obj->company_area . $obj->company_address ],
               ] ),

  Restful\ShowDate::create('建立日期', function ($obj) {
    return $obj->created_at;
  })
); ?>

</div>
