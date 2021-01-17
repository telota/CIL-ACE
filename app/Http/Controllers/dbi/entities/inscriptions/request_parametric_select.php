<?php

namespace App\Http\Controllers\dbi\entities\inscriptions;


class request_parametric_select {

    public function instructions ($user) {

        $select = [
            // ID
            'id'            => 'i.id',
            // Self
            'self'          => ['raw' => 'CONCAT("'.config('dbi.url.api').'inscriptions/", i.id)'],
            // Konkordanz
            'concordance'   => 'i.concordance',
            // Name
            'name_plain'    => 'i.name_plain',
            'name'          => 'i.name_formated'
        ];

        return $select;
    }
}
