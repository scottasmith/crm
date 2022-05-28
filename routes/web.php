<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$getTenant = function (\Illuminate\Http\Request $request): \App\Entities\Tenant {
    return $request->attributes->get('tenant');
};


Route::get('/', function (\Illuminate\Http\Request $request) use ($getTenant) {
    $tenant = $getTenant($request);

    dd($tenant->getUsers()->toArray());

    return view('welcome', ['tenant' => $request->attributes->get('tenant')]);
});
