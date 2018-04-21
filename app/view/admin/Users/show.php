<div class='back'>
  <a href="<?php echo RestfulUrl::index ();?>" class='icon-36'>回上一頁</a>
</div>

<div class='show-panel'>
<?php echo $show->appendRows(
  Restful\ShowText::create('姓名', function ($obj) {
    return $obj->name;
  }),
  Restful\ShowText::create('帳號', function ($obj) {
    return $obj->email;
  }),
  Restful\ShowImages::create('圖片', function ($obj) {
    return $obj->avatar->url();
  }),
  Restful\ShowText::create('電話', function ($obj) {
    return $obj->phone;
  }),
  Restful\ShowDate::create('生日', function ($obj) {
    return $obj->birthday;
  }),
  Restful\ShowText::create('居住城市', function ($obj) {
    return $obj->city;
  }),
  Restful\ShowText::create('簡介', function ($obj) {
    return $obj->brief;
  }),
  Restful\ShowText::create('專長', function ($obj) {
    return $obj->expertise;
  })
);
?>
</div>
