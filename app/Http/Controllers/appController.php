<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Request;
use Response;

class appController extends Controller {

    public function initiate () {

        return view ('layouts.app', ['app' => [
            'language'  => substr(Request::server('HTTP_ACCEPT_LANGUAGE'), 0, 2) === 'de' ? 'de' : 'en'
        ]]);
    }

    public function provideJS ($file) {
        $contents = \File::get('/opt/projects/'.(is_dir('/opt/projects/cil/src') ? 'cil' : 'cil-laravel').'/src/public/js/'.$file);
        $response = Response::make($contents, 200);
        $response->header('Content-Type', 'application/javascript');
        return $response;
    }
}
