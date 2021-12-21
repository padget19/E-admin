<?php
use think\facade\Route;
Route::get('/',function (){
    return view('/index',['web_name'=>sysconf('web_name')]);
});
