<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class edcs_update extends Command {
    protected $signature    = 'edcs:update';
    protected $description  = 'Get updated EDCS IDs';
    public function __construct() { parent::__construct(); }

    static $date = '210902';
    static $path = '/opt/projects/cil-laravel/output/';
    static $edcs = 'edcs-cil-links.txt';
    static $file = 'edcs.csv';

    // -----------------------------------------------------------------------
    public function handle() {
        date_default_timezone_set('Europe/Berlin');
        $time = date('U');
        $date = date('Y-m-d H:i:s');
        $import = self::$date;

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------------- START --------------------------\n".
            "----------------------------------------------------------\n\n"
        );

        // EDCS Liste
        echo 'Get List of EDCS_IDs ... ';
        $explode = file_get_contents(self::$path.self::$edcs);
        $explode = preg_split('/\r\n|\r|\n/', $explode);
        array_pop($explode);
        $edcs = [];
        foreach ($explode AS $e) {
            $e = explode("#", trim($e));
            $edcs[$e[0]] = $e[1];
        }
        echo count($edcs)." records\n";

        // KO Nummern
        echo 'Get List of Inscriptions ... ';
        $cil = DB::table('cil_fm.raw_'.$import.'_inscriptions')
        -> select([
            'fm_id as KO',
            'edcs_id as EDCS',
            DB::Raw('"nein" as up')
        ])
        -> get();
        $cil = json_decode($cil, true);
        echo count($cil)." records\n";

        $fp = fopen(self::$path.self::$file, 'w');
        fputcsv($fp, ['Konkordanznnumer', 'EDCS', 'Änderung']);

        // Processing
        echo 'Processing ... '."\n";
        foreach ($cil AS $i => $c) {
            if (!empty($edcs[$c['KO']]) && $edcs[$c['KO']] != $c['EDCS']) {
                $cil[$i]['EDCS'] = $edcs[$c['KO']];
                $cil[$i]['up'] = 'ja ('.(empty($c['EDCS']) ? 'kein Wert' : $c['EDCS']).' > '.$edcs[$c['KO']].')';
            }
            echo "\t".implode("\t", $cil[$i])."\n";
            fputcsv($fp, $cil[$i]);
        }
        echo "finished\n";

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
