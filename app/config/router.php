<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

Router::get ('', 'Main@index');

Router::get ('register/verify',     'Auth@verify');

Router::dir ('api', function () {
  Router::post('login', 'Auth@login');
  Router::post('login/facebook', 'Auth@facebookLogin');
  Router::post('login/google', 'Auth@googleLogin');
  Router::post('register', 'Auth@register');
  Router::post('forget', 'Auth@forget');

  Router::get('adv', 'Advs@adv');
  Router::post('adv', 'Advs@create');
  Router::put('adv', 'Advs@update');
  Router::get('advs', 'Advs@index');
  Router::post('adv/msg', 'Advs@createMsg');
  Router::get('adv/msgs', 'Advs@msgs');
  Router::post('adv/like', 'Advs@like');

  Router::get('brand', 'Brands@brand');
  Router::get('brands', 'Brands@index');
  Router::post('brand', 'Brands@create');
  Router::put('brand', 'Brands@update');
  Router::get('brand/products', 'Brands@products');
  Router::get('brand/product', 'Brands@product');
  Router::post('brand/product', 'Brands@createProduct');
  Router::put('brand/product', 'Brands@updateProduct');
  Router::get('brand/advs', 'Brands@advs');

  Router::get('user', 'Users@index');
  Router::put('user', 'Users@update');
  Router::get('user/brands', 'Users@brands');
  Router::get('user/advs', 'Users@advs');

  Router::get('notify', 'Notifies@index');
  Router::post('notify', 'Notifies@create');
  Router::put('notify', 'Notifies@update');

  Router::get('account', 'Accounts@index');
  Router::post('account', 'Accounts@create');
  Router::put('account', 'Accounts@update');

  Router::get('bank', 'Banks@index');

  Router::get('bonus/amount', 'Bonuses@amount');
  Router::get('bonus/advs', 'Bonuses@advs');
  Router::get('bonus/records', 'Bonuses@records');
  Router::post('bonus/receive', 'Bonuses@receive');
});

Router::dir ('admin', function () {
  Router::get ('', 'Main');

  Router::get ('login', 'Platform@login');
  Router::post ('login', 'Platform@acSignin');
  Router::get ('logout', 'Platform@logout');

  Router::get ('login', 'Platform@login');
  Router::post ('login', 'Platform@acSignin');
  Router::get ('logout', 'Platform@logout');

  Router::restful ('users', 'Users', array (
  array ('model' => 'User')));

  Router::restful ('admins', 'Admins', array (
    array ('model' => 'Admin')));

  Router::restful ('backups', 'Backups', array (
    array ('model' => 'Backup')));

  Router::restful ('advs', 'Advs', array (
    array ('model' => 'Adv')));

  Router::restful (array ('adv', 'messages'), 'AdvMessages', array (
    array ('model' => 'Adv'), array ('model' => 'AdvMessage')));

  Router::restful ('bonuses', 'Bonuses', array (
    array ('model' => 'Bonus')));

  Router::restful ('bonusReceives', 'BonusReceives', array (
    array ('model' => 'BonusReceive')));

  Router::restful (array ('bonusReceive', 'details'), 'BonusReceiveDetails', array (
    array ('model' => 'BonusReceive'), array ('model' => 'BonusReceiveDetail')));

  Router::restful ('brands', 'Brands', array (
    array ('model' => 'Brand')));

  Router::restful ('brandProducts', 'BrandProducts', array (
    array ('model' => 'BrandProduct')));

  Router::restful ('accounts', 'Accounts', array (
    array ('model' => 'Account')));

  Router::restful ('notifies', 'Notifies', array(
    array ('model' => 'Notify')));
});
