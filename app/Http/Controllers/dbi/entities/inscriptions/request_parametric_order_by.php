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
        $required = false;
        if (!empty($input['name'])) {
            if (in_array(substr($input['name'], 0, 1), ['I', 'V', 'X'])) {
                if (strlen($input['name']) == 1 || substr($input['name'], 1, 2) === ' ' || substr($input['name'], 1, 2) === ',') {
                    $required = true;
                }
            }
            if (in_array(substr($input['name'], 0, 2), ['I2', 'II', 'IV', 'VI', 'IX', 'XI', 'XV', 'XX'])) {
                $required = true;
            }
        }


        if ($param === 'index' && $required === true) {
            $explode = explode(' ', $input['name']);
            $string = trim($explode[0]);

            return $query -> orderByRaw(
                'CASE
                    WHEN
                        i.name_plain = "'.$string.'" OR
                        i.name_plain LIKE "'.$string.' %" OR
                        i.name_plain LIKE "'.$string.', %"
                    THEN CONCAT(1, '.$col.')
                    ELSE CONCAT(2, '.$col.')
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
