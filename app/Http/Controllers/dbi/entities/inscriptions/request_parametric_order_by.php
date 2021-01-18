<?php

namespace App\Http\Controllers\dbi\entities\inscriptions;

use DB;


class request_parametric_order_by {

    public function instructions () {
        return [
            'index' => 'i.sort_index'
        ];
    }

    public function process ($param, $col, $op, $query, $input) {
        if ($param === 'index' && !empty($input['name'])) {
            $explode = explode(' ', $input['name']);
            $string = trim($explode[0]);

            return $query -> orderByRaw(
                'CASE
                    WHEN
                        i.name_plain = "'.$string.'" OR
                        i.name_plain LIKE "'.$string.' %" OR
                        i.name_plain LIKE "'.$string.', %"
                    THEN CONCAT(0, '.$col.')
                    ELSE CONCAT(1, '.$col.')
                END'
                //'i.name_plain LIKE "'.$string.' %", '.   // NULLs last
                //$col.' '.$op      // order by given key and operator
            );
        }
        else {
            return $query -> orderByRaw(
                $col.' IS NULL, '.   // NULLs last
                $col.' '.$op      // order by given key and operator
            );
        }
    }
}
/*by case
    when name LIKE "%John%" then 1
    when name LIKE "%Doe%"  then 2
    else 3
end*/
