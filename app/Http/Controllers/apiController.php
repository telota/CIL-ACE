<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dbi\dbiManager;
use Response;

class apiController extends Controller {

    public function select ($entity, $id = NULL) {

        $user = ['id' => 0, 'level' => 2];
        $manager = new dbiManager();
        $dbi = $manager->select($user, $entity, $id);

        if (empty($dbi['error'])){
            // Detailed Response (if Contents is given by entity)
            if (isset($dbi['contents'])) {

                // Add url to pagination
                $dbi['pagination']['self']   = config('dbi.url.api').$entity.(empty($dbi['pagination']['self']) ? '' : ('?'.$dbi['pagination']['self']));
                $dbi['pagination']['pageOf'] = config('dbi.url.api').$entity.(empty($dbi['pagination']['pageOf']) ? '' : ('?'.$dbi['pagination']['pageOf']));
                foreach(['firstPage', 'previousPage', 'nextPage', 'lastPage'] as $i) {
                    $dbi['pagination'][$i] = (empty($dbi['pagination'][$i]) ? null : (config('dbi.url.api').$entity.'?'.$dbi['pagination'][$i]));
                }

                // return Response
                return Response::json([
                    'pagination' => isset($dbi['pagination']) ? $dbi['pagination'] : [],
                    'where' => [
                        'accepeted' => isset($dbi['where']['accepted']) ? $dbi['where']['accepted'] : [],
                        'ignored' => isset($dbi['where']['ignored']) ? $dbi['where']['ignored'] : []
                    ],
                    'contents'  => $dbi['contents']
                ], 200);
            }
            // Minimal Response (just array of results given)
            else {
                if (!isset($dbi[0]['published'])) {
                    if(empty($id)) {
                        return Response::json([
                            'pagination' => [
                                'self'  => config('dbi.url.api').$entity,
                                'count' => count($dbi)
                            ],
                            'contents' => $dbi
                        ], 200);
                    }
                    else {
                        if(!empty($dbi)) {
                            return Response::json(['contents' => $dbi], 200);
                        }
                        else {
                            return Response::json(['error' => $this->errorMessage($id, $entity, config('dbi.responses.api.no_content'))], 404);
                        }
                    }
                }
                else {
                    if ($dbi[0]['published'] == 1) {
                        unset($dbi[0]['published']);
                        return Response::json(['contents' => $dbi], 200);
                    }
                    elseif ($dbi[0]['published'] == 3) {
                        return Response::json(['error' => $this->errorMessage($id, $entity, config('dbi.responses.api.deleted'))], 403);
                    }
                    else {
                        return Response::json(['error' => $this->errorMessage($id, $entity, config('dbi.responses.api.not_published'))], 403);
                    }
                }
            }
        }
        else {
            return Response::json($dbi['error'], 404);
        }
    }
}
