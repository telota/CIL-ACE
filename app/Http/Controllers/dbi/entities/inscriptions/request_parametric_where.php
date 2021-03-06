<?php

namespace App\Http\Controllers\dbi\entities\inscriptions;


class request_parametric_where {

    public function instructions () {

        $where = [
            'id'                => 'i.id',
            'KO'                => 'i.concordance',

            'name'          =>  [
                'joins' => [
                    ['web_search_names AS wsn', 'wsn.id_inscription', '=', 'i.id']
                ],
                'orWhere' => [
                    ['wsn.search_string', '=', '', ''],
                    ['wsn.search_string', 'LIKE', '', '/%'],
                    ['wsn.search_string', 'LIKE', '', ' %'],
                    ['wsn.search_string', 'LIKE', '', ', %']
                ]
            ],

            'has_edcs'          => ['raw' => 'IF( i.edcs > "", 1, 0)'],
            'has_fotos'         => ['raw' => 'IF( i.fotos > "", 1, 0)'],
            'has_imprints'      => ['raw' => 'IF( i.imprints > "", 1, 0)'],
            'has_imprints_3d'   => ['raw' => 'IF( i.imprints_3d > "", 1, 0)'],
            'has_scheden'       => ['raw' => 'IF( i.scheden > "", 1, 0)']

            /*'has_edcs'          => [
                'where' => ['raw' => 'IF( r.edcs > "", 1, 0)'],
                'joins' => [
                    [config('dbi.tablenames.resources').' AS r', 'r.id', '=', 'i.id']
                ]
            ],
            'has_fotos'         => [
                'where' => ['raw' => 'IF( r.fotos > "", 1, 0)'],
                'joins' => [
                    [config('dbi.tablenames.resources').' AS r', 'r.id', '=', 'i.id']
                ]
            ],
            'has_imprints'      => [
                'where' => ['raw' => 'IF( r.imprints > "", 1, 0)'],
                'joins' => [
                    [config('dbi.tablenames.resources').' AS r', 'r.id', '=', 'i.id']
                ]
            ],
            'has_imprints_3d'   => [
                'where' => ['raw' => 'IF( r.imprints_3d > "", 1, 0)'],
                'joins' => [
                    [config('dbi.tablenames.resources').' AS r', 'r.id', '=', 'i.id']
                ]
            ],
            'has_scheden'       => [
                'where' => ['raw' => 'IF( r.scheden > "", 1, 0)'],
                'joins' => [
                    [config('dbi.tablenames.resources').' AS r', 'r.id', '=', 'i.id']
                ]
            ]*/
        ];

        return $where;



            /*'id'                    => 'c.id',
            'id_creator'            => 'c.id_creator',
            'id_editor'             => 'c.id_editor',

            'state_public'          => 'c.publication_state',

            'state_linked'          => [
                'where' => ['raw' => 'IF(ctt.id_coin IS NOT NULL, 1, 0)'],
                'joins' => [
                    [config('dbi.tablenames.coins_to_types').' AS ctt', 'ctt.id_coin', '=', 'c.id']
                ]
            ],

            'id_type'               => [
                'where' => 'ctt.id_type',
                'joins' => [
                    [config('dbi.tablenames.coins_to_types').' AS ctt', 'ctt.id_coin', '=', 'c.id']
                ]
            ],

            'state_inherited'       => [
                'where' => ['raw' => 'IF( cti.id_type IS NOT NULL, 1, 0 )'],
                'joins' => [
                    [config('dbi.tablenames.coins_inherit').' AS cti', 'cti.id', '=', 'c.id']
                ]
            ],

            // General
            'state_source'          => ['raw' => 'IF( c.source_link IS NOT NULL, 1, 0 )'],
            'source'                => ['c.source_link', 'LIKE', '%', '%'],

            'provenience'           => ['c.provenience', 'LIKE', '%', '%'],

            'state_comment_int'     => ['raw' => 'IF( c.comment_private > \'\', 1, 0)'],
            'comment_int'           => ['c.comment_private', 'LIKE', '%', '%'],

            'state_comment_ext'     => ['raw' => 'IF( c.comment_public > \'\', 1, 0)'],
            'comment_ext'           => ['c.comment_public', 'LIKE', '%', '%'],

            'description_private'   => ['c.description_private', 'LIKE', '%', '%'],

            'id_owner'              => 'c.id_owner_original',

            'id_reference'          => [
                'where' => 'cref.zotero_id',
                'joins' => [
                    [config('dbi.tablenames.coins_to_zotero').' AS cref', 'cref.id_coin', '=', 'c.id']
                ]
            ],

            'id_person'             => [
                'where' => 'ctp.id_person',
                'joins' => [
                    [config('dbi.tablenames.coins_to_persons').' AS ctp', 'ctp.id_coin', '=', 'c.id']
                ]
            ],

            'id_findspot'           => 'c.id_findspot',
            'id_hoard'              => 'c.id_hoard',

            // Production
            'id_mint'               => 'c.id_mint',
            'id_region'             => [
                'where' => 'sr.id_region',
                'joins' => [
                    [config('dbi.tablenames.mints').'                  AS mm', 'mm.id',                '=', 'c.id_mint'],
                    [config('dbi.tablenames.regions_to_subregions').'  AS sr', 'sr.nomisma_id_region', '=', 'mm.imported_nomisma_subregion']
                ]
            ],

            'id_authority'          => 'c.id_authority',
            'id_issuer'             => 'c.id_issuer',
            'id_authority_person'   => 'c.id_authority_person',
            'id_authority_group'    => 'c.id_authority_group',

            'id_material'           => 'c.id_material',
            'id_denomination'       => 'c.id_denomination',
            'id_standard'           => 'c.id_standard',

            'weight_start'          => ['c.weight', '>'],
            'weight_end'            => ['c.weight', '<'],

            'diameter_start'        => ['c.diameter_max', '>'],
            'diameter_end'          => ['c.diameter_max', '<'],

            'id_period'             => 'c.id_period',
            'date_start'            => ['c.date_start', '>'],
            'date_end'              => ['c.date_end',   '<'],

            'id_design'             => [
                ['raw' => 'CONCAT_WS("|", "c", c.id_design_o, c.id_design_r, "c")'], 'LIKE', '%|', '|%'
            ],
            'id_legend'             => [
                ['raw' => 'CONCAT_WS("|", "c", c.id_legend_o, c.id_legend_r, "c")'], 'LIKE', '%|', '|%'
            ],
            'id_monogram'           => [
                'where' => 'ctm.id_monogram',
                'joins' => [
                    [config('dbi.tablenames.coins_to_monograms').' AS ctm', 'ctm.id_coin', '=', 'c.id']
                ]
            ],
            'id_symbol'           => [
                'where' => 'cts.id_symbol',
                'joins' => [
                    [config('dbi.tablenames.coins_to_symbols').' AS cts', 'cts.id_coin', '=', 'c.id']
                ]
            ],
            'id_die'           => [
                ['raw' => 'CONCAT_WS("|", "c", c.id_die_o, c.id_die_r, "c")'], 'LIKE', '%|', '|%'
            ],

            // technical
            'img_id'                => [
                'where' => 'img.ImageID',
                'joins' => [
                    [config('dbi.tablenames.images').' AS img', 'img.CoinID', '=', 'c.id']
                ]
            ],
            'has_images'            => [
                ['raw' => '
                    IF( (SELECT 1
                    FROM '.config('dbi.tablenames.images').' AS img
                    WHERE
                        img.CoinID = c.id &&
                        CONCAT_WS("", img.ReverseImageFilename, img.ObverseImageFilename) IS NOT NULL
                    LIMIT 1
                    ) IS NOT NULL, 1, 0 )']
            ],
            'imported'              => [
                'where' => 'cs.import',
                'joins' => [
                    [config('dbi.tablenames.coins_submitted').' AS cs', 'cs.id', '=', 'c.id']
                ]
            ],
            'id_group'              => [
                'where' => 'cg.id_objectgroup',
                'joins' => [
                    [config('dbi.tablenames.coins_to_objectgroups').' AS cg', 'cg.id_coin', '=', 'c.id']
                ]
            ]
        ];

        // Obverse / Reverse
        foreach (['o', 'r'] AS $side) {
            $sides[$side] = [
                $side.'_id_design'           => 'c.id_design_'.$side,
                $side.'_id_legend'           => 'c.id_legend_'.$side,

                $side.'_design'              => [
                    'where' => ['d.design_de', 'LIKE', '%', '%'],
                    'connector' => 'AND',
                    'joins' => [
                        [config('dbi.tablenames.designs').' AS d', 'd.id', '=', 'c.id_design_'.$side]
                    ]
                ],

                $side.'_id_monogram'         => [
                    'where' => $side.'ctm.id_monogram',
                    'connector' => 'AND',
                    'joins' => [
                        [config('dbi.tablenames.coins_to_monograms').' AS '.$side.'ctm', $side.'ctm.id_coin', '=', 'c.id']
                    ],
                    'conditions' => [
                        [$side.'ctm.side', $side === 'o' ? 0 : 1]
                    ]
                ],

                $side.'_id_symbol'           => [
                    'where' => $side.'cts.id_symbol',
                    'connector' => 'AND',
                    'joins' => [
                        [config('dbi.tablenames.coins_to_symbols').' AS '.$side.'cts', $side.'cts.id_coin', '=', 'c.id']
                    ],
                    'conditions' => [
                        [$side.'cts.side', $side === 'o' ? 0 : 1]
                    ]
                ],
            ];
        }

        return array_merge($where, $sides['o'], $sides['r']);*/
    }
}
