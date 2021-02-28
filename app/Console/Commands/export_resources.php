<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class export_resources extends Command {
    protected $signature    = 'export:resources';
    protected $description  = 'Write all imported Resources to CSV';
    public function __construct() { parent::__construct(); }

    static $date = '210902';
    static $path = '/opt/projects/cil-laravel/output/';

    // -----------------------------------------------------------------------
    public function handle() {
        date_default_timezone_set('Europe/Berlin');
        $time = date('U');
        $date = date('Y-m-d H:i:s');
        $import = self::$date;
        $file_base = 'https://cil.bbaw.de/ace/files/';

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------------- START --------------------------\n".
            "----------------------------------------------------------\n\n"
        );

        // Fotos
        echo 'Get List of Fotos ... ';
        $dbi = DB::table('cil_fm.raw_'.$import.'_inscriptions_to_fotos AS itf')
        -> leftJoin('cil_fm.raw_'.$import.'_inscriptions AS b',  'b.id', '=', 'itf.id_inscription')
        -> leftJoin('cil_fm.raw_'.$import.'_fotos AS f',    'f.id', '=', 'itf.id_foto')
        -> select([
            'b.id AS i',
            'b.fm_id AS k',
            'f.fm_id AS n',
            DB::Raw('CONCAT(f.fm_id, ".jpg") AS d'),
            'f.is_public AS p',
            DB::Raw('"Foto" AS t')
        ])
        -> get();
        $resources['fotos'] = json_decode($dbi, true);
        echo count($dbi)." records\n";

        // Imprints
        echo 'Get List of Imprints ... ';
        $dbi = DB::table('cil_fm.raw_'.$import.'_inscriptions_to_imprints AS iti')
        -> leftJoin('cil_fm.raw_'.$import.'_inscriptions AS b',  'b.id', '=', 'iti.id_inscription')
        -> leftJoin('cil_fm.raw_'.$import.'_imprints AS i', 'i.id', '=', 'iti.id_imprint')
        -> select([
            'b.id AS i',
            'b.fm_id AS k',
            'i.fm_id AS n',
            DB::Raw('CONCAT("P", i.fm_id, ".jpg") AS d'),
            DB::Raw('1 AS p'),
            DB::Raw('"Abklatsch" AS t')
        ])
        -> get();
        $resources['imprints'] = json_decode($dbi, true);
        echo count($dbi)." records\n";

        // Scheden
        echo 'Get List of Scheden ... ';
        $dbi = DB::table('cil_fm.raw_'.$import.'_inscriptions_to_scheden AS its')
        -> leftJoin('cil_fm.raw_'.$import.'_inscriptions AS b',  'b.id', '=', 'its.id_inscription')
        -> leftJoin('cil_fm.raw_'.$import.'_scheden AS s',  's.id', '=', 'its.id_schede')
        -> select([
            'b.id AS i',
            'b.fm_id AS k',
            's.fm_id AS n',
            DB::Raw('CONCAT(s.fm_id, ".jpg") AS d'),
            DB::Raw('1 AS p'),
            DB::Raw('"Schede" AS t')
        ])
        -> get();
        $resources['scheden'] = json_decode($dbi, true);
        echo count($dbi)." records\n";

        // Server Index
        echo 'Get Server Index ... ';
        $index = json_decode(file_get_contents(self::$path.'index_2021-02-26.json'), true);
        echo count($index)." records\n";

        // Processing
        echo 'Processing ... ';
        $items = [];
        foreach ($resources AS $r) {
            foreach ($r AS $i) {
                $found = false;
                if ($i['p'] === 1) { $found = in_array($i['d'], $index) ? true : false; }

                echo "\t".implode(', ', array_merge($i, [$found === true ? 1 : 0]))."\n";

                $items[$i['i']][$i['n']] = [
                    $i['k'],
                    $i['t'],
                    $i['n'],
                    ($found === true ? $i['d'] : null),
                    ($found === true ? $file_base.$i['d'] : null),
                    ($i['p'] === 0 ? 'nicht öffentlich, nur auf Anfrage' : ($found === false ? 'noch nicht digitalisiert' : null))
                ];
            }
        }
        ksort($items);
        echo count($items)." records\n";

        // Writing CSV
        echo("\n".'WRITING CSV FIle ... '."\n");
        $fp = fopen(self::$path.'cil_ace_'.self::$date.'_resources.csv', 'w');

        fputcsv($fp, ['Konkordanznnumer', 'Ressource', 'Nummer', 'Dateiname', 'Link', 'Anmerkung']);

        foreach ($items AS $item) {
            ksort($item);
            foreach ($item AS $i) {
                echo "\t".implode(', ', $i)."\n";
                fputcsv($fp, $i);
            }
        }
        fclose($fp);
        echo("SUCCESS\n");

        echo("\n\nExecution time: ".(date('U') - $time)." sec\n");
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }
}
