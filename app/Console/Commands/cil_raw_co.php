<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_raw_co extends Command
{
    protected $signature    = 'cil:raw_co';
    protected $description  = 'red co table';
    public function __construct() { parent::__construct(); }

    static $co_csv = '/opt/projects/cil-laravel/fm/co.CSV';

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
            $record['id'] = intval(substr($raw['Konkordanznummer'], 2, 9));
            $record['concordance'] = substr($raw['Konkordanznummer'], 0, 9);
            $record['name_plain'] = trim($raw['Inschriftnummer']);
            $record['edcs'] = trim($raw['Clauss_ID_19']);
            // Last Edit
            $ea = explode('.', trim($raw['Bearbeitet am']));
            $record['edited_at'] = $ea[2]."-".$ea[1]."-".$ea[0];


            echo("\t".$record['concordance'].", ".$record['edited_at']."\n");
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
                    //echo("\n    ".$new_row['Konkordanznummer'].": ");
                    if (substr($new_row['Konkordanznummer'], 0, 2) === 'KO' && !empty($new_row['Inschriftnummer']) && (!empty($new_row['Schede01']) || !empty($new_row['Abklatsch 01']) || !empty($new_row['Photothek 01']))) {
                        $data[] = $new_row;
                        //echo("found");
                        echo('1');
                    }
                    else {
                        //echo("not found");
                        echo('0');
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
