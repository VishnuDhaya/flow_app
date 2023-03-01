<?php

//Route::post('/leads/login','App\Http\Livewire\Leadsignin@loginUser')->name("leadlogin");
Route::get('/leads/login',function (){
    return view("lead");
})->middleware('guest');

Route::group(['middleware' =>  ['leadportal']], function () {

    Route::get('/leads/home',function (){
        return view("home");
    })->name('leadhome');
    Route::get('/leads/create',function (){
        return view("create");
    })->name('leadcreate');
    Route::get('/leads/search',function (){
        return view("search");
    })->name('leadsearch');
    Route::get('/leads/update',function (){
        return view("update");
    })->name('leadupdate');
});