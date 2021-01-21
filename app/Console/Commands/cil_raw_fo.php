<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_raw_fo extends Command
{
    protected $signature    = 'cil:raw_fo';
    protected $description  = 'read Foto Table';
    public function __construct() { parent::__construct(); }

    static $co_csv = '/opt/projects/cil-laravel/fm/fo.CSV';

    // -----------------------------------------------------------------------
    public function handle() {
        $time = date('U');

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------------- START --------------------------\n".
            "----------------------------------------------------------\n\n"
        );

        // Preparations
        $records = [];

        // Get Array of Inscriptions and their resources
        $parsed_csv = self::csv_to_array(self::$co_csv, ';');
        echo("\n\nSTARTED EXTRACTION\n\n");

        // Extract data from parsed CSV
        foreach ($parsed_csv AS $raw) {
            $record = [];
            $record['id'] = intval(substr($raw['CIL_Inventar'], 2, 9));
            $record['fm_id'] = substr($raw['CIL_Inventar'], 0, 9);
            $record['author'] = empty($raw['Photograph']) || $raw['Photograph'] === 'Unbekannt' ? null : trim($raw['Photograph']);
            $record['year'] = empty($raw['AUFNAHME_JAHR']) || $raw['AUFNAHME_JAHR'] === '5555' ? null : intval(trim($raw['AUFNAHME_JAHR']));
            $record['public'] = $raw['Nicht_Internet'] === 'Nicht Internet' ? 0 : 1;

            echo("\t".$record['fm_id'].", ".$record['author']." - ".$record['year']."\n");
            $records[] = $record;
        }

        // Regular End of Script -------------------------------------------------------------------------------------
        echo("\nNumber of records: ".count($records)."\n");
        echo("\nExecution time: ".(date('U') - $time)." sec\n");
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }

    static function csv_to_array ($filename='', $delimiter=',') {
        //self::GetFileContent($filename);

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            echo("\nSTARTED PARSING\n");
            while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                if(!$header) {
                    $header = $row;
                }
                else {
                    $new_row = array_combine($header, $row);
                    if (substr($new_row['CIL_Inventar'], 0, 2) === 'PH') {
                        $data[] = $new_row;
                        echo('.');
                    }
                }
            }
            fclose($handle);
            echo("\nFINISHED PARSING\n");
        }
        return $data;
    }

    static function GetFileContent ($file) {
        echo("\nTry to read '$file'...\n");

        if ($content = file_get_contents($file)) {
            echo("SUCCESS: '$file' was read.\n\n");
            return $content;
        } else {
            die("ERROR: Unable to open '$file'!\n\n");
        }
    }
}
