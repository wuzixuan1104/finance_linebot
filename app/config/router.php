<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

Router::get ('', 'Main@index');

Router::dir ('api', function () {
  Router::post('login', 'Auth@login');
  Router::post('login/facebook', 'Auth@facebookLogin');
  Router::post('login/google', 'Auth@googleLogin');
  Router::post('register', 'Auth@register');
  Router::post('forget', 'Auth@forget');

  Router::get('adv', 'Advs@adv');
  Router::post('adv', 'Advs@create');
  Router::get('advs', 'Advs@index');
  Router::post('adv/msg', 'Advs@createMsg');
  Router::get('adv/msgs', 'Advs@msgs');
  Router::post('adv/like', 'Advs@like');

  Router::get('brand', 'Brands@brand');
  Router::get('brands', 'Brands@brands');
  Router::post('brand', 'Brands@create');
  Router::get('brand/products', 'Brands@products');
  Router::get('brand/product', 'Brands@product');

  Router::get('user', 'Users@index');
  Router::put('user', 'Users@update');

  Router::get('notify', 'Notifies@notify');
  Router::post('notify', 'Notifies@create');
  Router::put('notify', 'Notifies@update');

});

Router::dir ('admin', function () {
  Router::get ('', 'Main');

  Router::get ('login', 'Platform@login');
  Router::post ('login', 'Platform@acSignin');
  Router::get ('logout', 'Platform@logout');

  Router::get ('login', 'Platform@login');
  Router::post ('login', 'Platform@acSignin');
  Router::get ('logout', 'Platform@logout');


  Router::restful ('backups', 'Backups', array (
    array ('model' => 'Backup')));
});
