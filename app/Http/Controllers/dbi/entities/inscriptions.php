<?php

namespace App\Http\Controllers\dbi\entities;

use App\Http\Controllers\dbi\dbiInterface;
use Request;

use App\Http\Controllers\dbi\handler\complex_select;


class inscriptions implements dbiInterface  {

    // Controller-Functions ------------------------------------------------------------------

    public function select ($user, $id) {
        $input = Request::post();

        // Add sort-by if not given
        if (!isset($input['sort_by'])) { $input['sort_by'] = 'index'; }
        // Add Limit if not given
        if (!isset($input['limit'])) { $input['limit'] = 50; }

        // Process Name
        if (!empty($input['name'])) {
            $name = $input['name'];

            $name = str_replace(['[', ']'], '', $name);

            if (substr(strtolower($name), 0, 4) === 'cil ') { $name = substr($name, 4); }

            $name = str_replace('²', '2', $name);
            $name = str_replace('³', '3', $name);

            $input['name'] = $name;
        }

        $handler = new complex_select;
        return $handler -> handleRequest('inscriptions', $user, $input, $id);
    }


    public function input ($user, $input) { die (abort(404, 'Not supported!')); }

    public function delete ($user, $input) { die (abort(404, 'Not supported!')); }

    public function connect ($user, $input) { die (abort(404, 'Not supported!')); }


    // Helper-Functions ------------------------------------------------------------------

    public function validateInput ($input) { }
}
