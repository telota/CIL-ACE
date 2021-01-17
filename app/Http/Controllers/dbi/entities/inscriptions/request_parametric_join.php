<?php

namespace App\Http\Controllers\dbi\entities\inscriptions;


class request_parametric_join {

    public function instructions ($query) {
        return $query;//->leftJoin(config('dbi.tablenames.resources').' AS r', 'r.id', '=', 'i.id');
    }
}
