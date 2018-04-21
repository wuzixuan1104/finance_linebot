<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('審核')->setWidth (60)->setClass ('center')->setTd (function ($obj, $column) { return $column->setSwitch ($obj->review == Adv::REVIEW_PASS, array ('class' => 'switch ajax', 'data-column' => 'review', 'data-url' => RestfulURL::url ('admin/Advs@review', $obj), 'data-true' => Adv::REVIEW_PASS, 'data-false' => Adv::REVIEW_FAIL)); }),
  Restful\Column::create ('ID')->setWidth (50)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('類型')->setWidth (60)->setTd (function ($obj) { return Adv::$typeTexts[$obj->type]; }),
  Restful\Column::create ('標題')->setWidth (150)->setTd (function ($obj) { return $obj->title; }),
  Restful\Column::create ('會員名稱')->setWidth (100)->setTd (function ($obj) { return $obj->user->name; }),
  Restful\Column::create ('會員信箱')->setWidth (200)->setTd (function ($obj) { return $obj->user->email; }),
  Restful\Column::create ('瀏覽數')->setWidth (60)->setSort ('cnt_view')->setTd (function ($obj) { return number_format ($obj->cnt_view); }),
  Restful\Column::create ('留言數')->setWidth (60)->setSort ('cnt_message')->setTd (function ($obj) { return '<a href="' . RestfulUrl::url ('admin/AdvMessages@index', $obj) . '">' . number_format ($obj->cnt_message) . '</a>'; }),
  Restful\Column::create ('喜歡數')->setWidth (60)->setSort ('cnt_like')->setTd (function ($obj) { return number_format ($obj->cnt_like); }),
  Restful\EditColumn::create ('編輯')->setTd (function ($obj, $column) {
    return $column->addLink (RestfulUrl::show ($obj), array ('class' =>'icon-29'));
    })
  );
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
