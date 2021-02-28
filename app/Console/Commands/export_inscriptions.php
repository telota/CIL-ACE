<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class export_inscriptions extends Command {
    protected $signature    = 'export:inscriptions';
    protected $description  = 'Write all imported inscriptions to CSV';
    public function __construct() { parent::__construct(); }

    static $date = '210902';
    static $path = '/opt/projects/cil-laravel/output/';

    // -----------------------------------------------------------------------
    public function handle() {
        date_default_timezone_set('Europe/Berlin');
        $time = date('U');
        $date = date('Y-m-d H:i:s');

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------------- START --------------------------\n".
            "----------------------------------------------------------\n\n"
        );

        echo 'Get List of inscriptions ... ';
        $dbi = DB::table('cil_fm.raw_'.self::$date.'_inscriptions')
        -> select([
            'fm_id AS Konkordanznummer',
            'id AS ID',
            DB::Raw('
                CONCAT("https://cil.bbaw.de/ace/id/", id) AS Link
            '),
            'name_plain AS Name',
            'edcs_id AS EDCS',
            'edited_at AS Stand'
        ])
        -> get();
        $dbi = json_decode($dbi, true);
        echo count($dbi)." records\n";

        echo('WRITING CSV FIle ... '."\n");
        $fp = fopen(self::$path.'cil_ace_'.self::$date.'_inscriptions.csv', 'w');

        fputcsv($fp, array_keys($dbi[0]));

        foreach ($dbi AS $item) {
            $item['Name'] = str_replace(["\r\n","\r","\n", "\n\r"], '', $item['Name']);
            echo "\t".$item['Konkordanznummer']."\n";
            fputcsv($fp, array_values($item));
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
