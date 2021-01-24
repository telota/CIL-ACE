<?php

namespace App\Http\Controllers\dbi\entities\inscriptions;

use DB;


class request_id_join {

    public function instructions ($query) {
        return $query; //->leftJoin(config('dbi.tablenames.resources').' AS r', 'r.id', '=', 'i.id');
    }
}
