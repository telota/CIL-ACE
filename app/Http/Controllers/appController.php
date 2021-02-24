<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Request;
use Response;

class appController extends Controller {

    public function initiate () {
        return view ('layouts.app', [
            'app' => [
                'language'  => substr(Request::server('HTTP_ACCEPT_LANGUAGE'), 0, 2) === 'de' ? 'de' : 'en'
            ]
        ]);
    }

    public function uri ($param) {
        if (strtolower(substr($param, 0, 2)) === 'ko') { $param = substr($param, 2); }
        $id = intval($param);

        if ($id > 0) {
            return redirect('/ace#/ko/'.$id);
        }
        else {
            die (abort(404, "No valid ID given."));
        }
    }
}
