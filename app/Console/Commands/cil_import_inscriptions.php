<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_import_inscriptions extends Command
{
    protected $signature    = 'cil:import_inscriptions';
    protected $description  = 'Process CIL FM Export and write to MySQL-DB';
    public function __construct() { parent::__construct(); }


    // -----------------------------------------------------------------------
    static $latest_import = 210120;
    static $start = 0;
    static $end = 1000000;
    static $delimiters = [
        '= cf.',
        '[=',
        '(=)',
        '(=',
        '(+',

        '=',
        'cf.',
        'et',
        '+',

        '(ubi err. ad',
        '(ubi err.',
        '(err. ad',
        '(err.',

        'ubi err. ad',
        'ubi err.',
        'err. ad',
        'err.',
        'ad',
    ];


    static $table_base_inscriptions = 'web_base_inscriptions';
    static $table_search_names      = 'web_search_names';
    static $path                    = '/opt/projects/cil-laravel/output/';
    static $file_base_inscriptions  = '/opt/projects/cil-laravel/output/web_base_inscriptions.sql';
    static $file_search_names       = '/opt/projects/cil-laravel/output/web_search_names.sql';

    static $file_errors             = '/opt/projects/cil-laravel/output/errorlog.csv';

    // -----------------------------------------------------------------------
    public function handle()
    {
        $time = date('U');

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "---------------- CIL INSCRIPTION IMPORTER ----------------\n".
            "----------------------------------------------------------\n\n"
        );

        // Preparations
        $i          =   $success = $i_search_name = 0;
        $base_inscriptions = $search_names =   $duplicates = $empties = $problems = [];
        $start      =   self::$start;
        $end        =   self::$end;
        $editions   =   self::GetEditions();
        //file_put_contents(self::$file_errors, "Konkordanznummer;Name;Fehlermeldung\n");

        // Iterate over Inscriptions
        echo('PROCESSING - START');
        foreach (self::GetInscriptions() as $row) {
            if($i >= $start && $i < $end) {

                // ID / Concordance
                $id = $row['id']; // id is equal concordance
                $co = $row['fm_id']; // concordance

                // Basic Processing of raw name
                $row['name_plain'] = str_replace("\r", " ", $row['name_plain']);
                $row['name_plain'] = str_replace("\n", " ", $row['name_plain']);
                $row['name_plain'] = str_replace("  ", " ", $row['name_plain']);
                $name_raw = $row['name_plain'];

                // Go into record
                if (
                    !empty($co) &&
                    !empty($name_raw) &&
                    substr($name_raw, 0,1) != '-' // sometimes used to mark work in progress
                ) {

                    if(!isset($base_inscriptions[$id])) {
                        // Advanced Processing of raw name
                        $name_raw = self::ProcessRawName($name_raw);

                        // Parse and split Name
                        $parsing = self::ParseName($name_raw, $editions);

                        // Check Parsing result
                        foreach($parsing as $v) {
                            if ($v['position'] == 1 && empty($v['id_edition'])) {
                                $error = true;
                                $errors[] = [true, $row, 'Unbekannter Leitbeleg \''.$v['search_string'].'\''];
                                //$error = 'FEHLER: Unbekannter Leitbeleg \''.$v['search_string'].'\'';
                            }
                            else if (strlen($v['search_string']) > 255) {
                                $error = true;
                                $errors[] = [true, $row, 'Ermittelte Referenz zu lang - mutmaßlicher Lesefehler'];
                                //$error = 'FEHLER: Ermittelte Referenz zu lang - mutmaßlicher Lesefehler';
                            }
                        }

                        if (!isset($error)) {
                            $rebuilt = self::RebuildName($parsing);
                            // ----------------------------------------------------------------------------------------------------

                            // Write to Base-Inscriptions-Array
                            $base_inscriptions[$id] = '('.implode(',', [
                                $id,                            // id
                                "'".$co."'",                    // concordance
                                "'".trim(str_replace("'", "\\'", $name_raw))."'",                   // name_plain
                                "'".trim(str_replace("'", "\\'", $rebuilt['name_formated']))."'",   // name_formated
                                "'".trim(str_replace("'", "\\'", $rebuilt['name_object']))."'",     // name_object
                                "'".$rebuilt['sort_index']."'"  // sort_index
                            ]).')';

                            // Write to Search-Name-Array
                            foreach ($parsing as $v) {
                                ++$i_search_name;
                                $search_names[$i_search_name] = '('.implode(',', [
                                    $i_search_name,             // id
                                    $id,                        // id_inscription
                                    $v['position'],             // position
                                    ($v['position'] != 1 ? ("'".$v['delimiter']."'") : 'null'), // delimiter
                                    "'".trim(str_replace("'", "\\'", $v['search_string']))."'", // search_string
                                    (!empty($v['id_edition']) ? $v['id_edition'] : 'null')      // id_edition
                                ]).')';

                                if (empty($v['id_edition'])) {
                                    $errors[] = [false, $row, 'Unbekannter Beleg \''.$v['search_string'].'\' an Position '.$v['position']];
                                    //self::WriteErrorLog(self::$file_errors, $row, 'WARNUNG: Unbekannter Beleg \''.$v['search_string'].'\' an Position '.$v['position']);
                                }
                            }

                            // ----------------------------------------------------------------------------------------------------

                            // Report
                            echo("\t". sprintf('%06d', $i) ." ". sprintf('%06d', $id) ." ". $co ." ". $name_raw ."\n");

                            ++$success;
                        }

                        else {
                            $problems [] = [
                                'co'    =>  $co,
                                'name'  =>  $name_raw
                            ];

                            //self::WriteErrorLog(self::$file_errors, $row, $error);
                            unset($error);
                        }
                    }
                    // ----------------------------------------------------------------------------------------------------

                    // Duplicate detected
                    else {
                        $duplicates [] = [
                            'co'    =>  $co,
                            'name'  =>  $name_raw
                        ];
                        $errors[] = [true, $row, 'Doppelte Konkordanznummer'];

                        //self::WriteErrorLog(self::$file_errors, $row, 'FEHLER: Doppelte Konkordanznummer');
                    }
                }

                // CO and/or Name is empty
                else {
                    $empties [] = [
                        'co'    =>  $co,
                        'name'  =>  $name_raw
                    ];
                    $errors[] = [true, $row, 'Konkordanz oder Name ist leer bzw. ungültig'];

                    //self::WriteErrorLog(self::$file_errors, $row, 'FEHLER: Konkordanz oder Name ist leer bzw. ungültig');
                }
            }

            ++$i; // Increment
        }
        echo("\n\n".'PROCESSING - END'."\n\n");

        // ----------------------------------------------------------------------------------------------------------------

        // Report Total and Imported
        echo("\nSUCCESS: Data analyzed:\n".
            "\ttotal: $i\n".
            "\timported: $success\n"
        );

        // Report empty Records
        echo("\tempties: ".count($empties)."\n");
        foreach($empties as $empty) {
            echo("\t\t".$empty['co']." : ".$empty['name']."\n");
        }

        // Report duplicates
        echo("\tduplicates: ".(count($duplicates) / 2)."\n");
        foreach($duplicates as $duplicate) {
            echo("\t\t".$duplicate['co']." : ".$duplicate['name']."\n");
        }

        // Report Problems
        echo("\tproblems: ".(count($problems))."\n");
        foreach($problems as $problem) {
            echo("\t\t".$problem['co']." : ".$problem['name']."\n");
        }
        // ----------------------------------------------------------------------------------------------------------------

        // Write Files
        echo("\n");
        self::WriteBaseInscriptions($base_inscriptions);
        self::WriteSearchNames($search_names);
        if (isset($errors)) { self::WriteErrorLog($errors); }

        echo("\n\nTotal execution time: ".(date('U') - $time)." sec\n");


        // Regular End of Script -------------------------------------------------------------------------------------
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }


    static function ProcessRawName ($name) {

        $name = str_replace('cf ', 'cf. ', $name); // add dot to missspelled "confer"

        return $name;
    }


    static function ParseName ($name, $editions) {

        $delimiters = self::$delimiters;

        // Add blank spaces to Delimiters
        $delimiters = array_map(function($item) {
            return ' '.$item.' ';
        }, $delimiters);

        // General error replacing
        $name = str_replace(' = = ', ' = ', $name); // Delete double equal
        $name = str_replace('cf ', 'cf. ', $name); // add dot to missspelled "confer"

        // Handle Exceptions
        if (substr($name, 0, 24) === '(Res gestae Divi Augusti') {
            $items = self::handle_rgda($name, $editions);
        }
        else {
            // split name
            $splitted = self::split($delimiters, $name);
            $splitted = self::GetDelimiters($splitted, $name);

            // Analyze Split
            foreach($splitted AS $split) {
                $analyzed   = self::AnalyzeReference($split['reference'], $editions);
                $sort_by    = $split['position'] == 1 ? self::MakeSorting($analyzed['id_edition'], $analyzed['quote_plain']) : null;

                $items[] = [
                    'position'          => $split['position'],
                    'delimiter'         => $split['delimiter'],
                    'ref_original'      => $split['reference'],
                    'search_string'     => $analyzed['search_string'],
                    'id_edition'        => $analyzed['id_edition'],
                    'ref_short'         => $analyzed['short'],
                    'ref_full'          => $analyzed['full'],
                    'quote_plain'       => $analyzed['quote_plain'],
                    'quote_formated'    => $analyzed['quote_formated'],
                    'start_clamped_by'  => $analyzed['start_clamped_by'],
                    'end_clamped_by'    => $analyzed['end_clamped_by'],
                    'sort_by'           => $sort_by
                ];
            }
        }

       //die("\n".print_r($items)."\n");

        return isset($items) ? $items : [];
    }


    static function AnalyzeReference ($ref, $editions) {

        // Correct typical errors
        $ref = self::CorrectTypicalErrors($ref);

        // Check for parentheses and $brackets at start
        if (substr($ref, 0, 2) === '((' || substr($ref, 0, 2) === '([') {
            $start_clamped_by = substr($ref, 0, 2);
        }
        else if (substr($ref, 0, 1) === '(' || substr($ref, 0, 1) === '[') {
            $start_clamped_by = substr($ref, 0, 1);
        }

        // Check for parentheses and $brackets at start
        if (substr($ref, -2) === '])') {
            $end_clamped_by = substr($ref, -2);
        }
        else if (substr($ref, -1) === ')' || substr($ref, -1) === ']') {
            $end_clamped_by = substr($ref, -1);
        }

        // Apply Clamping-Analysis on search string
        $search_string = isset($start_clamped_by) ? ltrim($ref, $start_clamped_by) : $ref;
        $search_string = isset($end_clamped_by) ? rtrim($search_string, $end_clamped_by) : $search_string;

        // Iterate over editions to find matching edition
        foreach($editions as $edition) {

            $ed_length = strlen($edition['a']);

            // get delimiter of edition and quote (either space or comma+space)
            if( substr(strtolower($search_string), 0, ($ed_length +1)) === strtolower($edition['a']).' ') {
                $matching_edition['delimited_by'] = ' ';
            }
            else if ( substr(strtolower($search_string), 0, ($ed_length +2)) === strtolower($edition['a']).', ') {
                $matching_edition['delimited_by'] = ', ';
            }

            if(isset($matching_edition)) {

                $matching_edition['id']     = $edition['id'];
                $matching_edition['a']      = $edition['a'];
                $matching_edition['name']   = $edition['name'];

                // stop loop
                break;
            }
        }

        // Matching Edition has been found
        if(isset($matching_edition)) {

            // set edition ID
            $id = $matching_edition ['id'];

            // get plain quote
            $quote_plain = substr($ref, ($ed_length + strlen($matching_edition['delimited_by'])));
            $quote_plain = trim(isset($end_clamped_by) ? rtrim($quote_plain, $end_clamped_by) : $quote_plain);

            // get formated quote
            $quote_formated = self::FormatQuote($quote_plain);

            // set short reference
            $ref_short = self::FormatReference($matching_edition['a']).$matching_edition['delimited_by'].$quote_formated;

            // set full reference
            $ref_full = $matching_edition['name'].', '.$quote_formated;
        }

        // Matching Edition has not been found
        else {
            $id = $quote_plain = $quote_formated = null;
            $ref_short = $ref_full = $sort_by = $search_string;
        }

        return [
            'search_string'     => $search_string,
            'reference'         => $ref,
            'id_edition'        => $id,
            'quote_plain'       => $quote_plain,
            'quote_formated'    => $quote_formated,
            'short'             => $ref_short,
            'full'              => $ref_full,
            'start_clamped_by'  => isset($start_clamped_by) ? $start_clamped_by : '',
            'end_clamped_by'    => isset($end_clamped_by) ? $end_clamped_by : ''
        ];
    }


    static function CorrectTypicalErrors ($ref) {

        $ref = str_replace(' /', '/', $ref); // sometimes there is a space before / like in "II2 /5"

        if (substr($ref, 0, 4) === 'CIL ')                  { $ref = substr($ref, 4); }

        else if (substr($ref, 0, 4) === 'HEp ')             { $ref = 'HEp. '.substr($ref, 4); }

        else if (substr($ref, 0, 8) === 'ILN I Fr')         { $ref = 'ILN I Frèjus '.substr($ref, 13); }

        else if (substr($ref, 0, 9) === 'ILAlg.II ')        { $ref = 'ILAlg. II '.substr($ref, 9); }
        else if (substr($ref, 0, 7) === 'ILalg. ')          { $ref = 'ILAlg. '.substr($ref, 7); }
        else if (substr($ref, 0, 6) === 'ILAlg ')           { $ref = 'ILAlg. '.substr($ref, 6); }
        else if (substr($ref, 0, 8) === 'ILAllg. ')         { $ref = 'ILAlg. '.substr($ref, 8); }

        else if (substr($ref, 0, 7) === 'Mourir ')          { $ref = 'Mourir à Dougga '.substr($ref, 16); }

        else if (substr($ref, 0, 9) === 'Suppl.It ')        { $ref = 'Suppl. It. '.substr($ref, 9); }
        else if (substr($ref, 0, 9) === 'Suppl It ')        { $ref = 'Suppl. It. '.substr($ref, 9); }
        else if (substr($ref, 0, 10) === 'Suppl.It. ')      { $ref = 'Suppl. It. '.substr($ref, 10); }
        else if (substr($ref, 0, 10) === 'Suppl It. ')      { $ref = 'Suppl. It. '.substr($ref, 10); }
        else if (substr($ref, 0, 10) === 'Supp. It. ')      { $ref = 'Suppl. It. '.substr($ref, 10); }
        else if (substr($ref, 0, 11) === 'Suppl. It, ')     { $ref = 'Suppl. It. '.substr($ref, 11); }

        return $ref;
    }


    static function handle_rgda ($name, $editions) {
        $explode = explode(') III', $name);
        $start = $explode[0].') ';
        $i = 1;
        $found_delimiters = [];

        foreach(explode('III', $explode[1]) as $item) {
            $ref_original   = trim('III'.$item);
            $ref_cut        = $ref_original;
            $end_clamped_by = null;

            if (substr($ref_cut, -1) === '.' && substr($ref_cut, -2) !== 'f.') {
                $end_clamped_by = substr($ref_cut, -1);
                $ref_cut = substr($ref_cut, 0, -1);
            }
            else if ($i > 1) {
                $space_explode = explode(" ", $ref_cut);
                $last = trim(array_pop($space_explode));
                foreach (self::$delimiters as $delimiter) {
                    if ($last === $delimiter) {
                        $found_delimiters[$i + 1] = $delimiter;
                        $ref_cut = trim(implode(" ", $space_explode));
                    }
                }
            }

            $analyzed       = self::AnalyzeReference($ref_cut, $editions);
            $sort_by        = $i == 1 ? self::MakeSorting($analyzed['id_edition'], $analyzed['quote_plain']) : null;

            $items[] = [
                'position'          => $i,
                'delimiter'         => empty($found_delimiters[$i]) ? null : $found_delimiters[$i],
                'ref_original'      => $ref_original,
                'search_string'     => $analyzed['search_string'].(substr($ref_cut, -1) === ')' ? ')' : ''),
                'id_edition'        => $analyzed['id_edition'],
                'ref_short'         => $analyzed['short'].(substr($ref_cut, -1) === ')' ? ')' : ''),
                'ref_full'          => $analyzed['full'].(substr($ref_cut, -1) === ')' ? ')' : ''),
                'quote_plain'       => $analyzed['quote_plain'].(substr($ref_cut, -1) === ')' ? ')' : ''),
                'quote_formated'    => $analyzed['quote_formated'].(substr($ref_cut, -1) === ')' ? ')' : ''),
                'start_clamped_by'  => null,
                'end_clamped_by'    => $end_clamped_by,
                'sort_by'           => $sort_by
            ];
            ++$i;
        }

        // Add Name as clamping start
        $items[0]['start_clamped_by'] = $start;
//die(print_r($items));
        return $items;
    }


    static function split ($delimiters, $string) {

        //if neither the delimiter nor the string are arrays
        if(!is_array(($delimiters)) && !is_array($string)){

            $items = explode($delimiters, $string);

            return self::MapAndTrim($items);
        }

        //if the delimiter is not an array but the string is
        else if(!is_array($delimiters) && is_array($string)) {

            foreach($string as $item) {

                foreach(explode($delimiters, $item) as $sub_item){
                    $items[] = $sub_item;
                }
            }

            return self::MapAndTrim($items);
        }

        //if the delimiter is an array but the string is not
        else if(is_array($delimiters) && !is_array($string)) {

            $string_array[] = $string;

            foreach($delimiters as $delimiter){
                $string_array = self::split($delimiter, $string_array);
            }

            return $string_array;
        }
    }


    static function GetDelimiters ($references, $string) {

        // iterate over array of found references and get delimiters
        for ($i = 0; $i < count($references); $i++) {

            if($i === 0) {

                $items [] = [
                    'reference' => $references[0],
                    'delimiter' => null,
                    'position'  => 1
                ];

                $string = substr($string, strlen($references[0]));
            }
            else {

                if (!empty($references[$i])) {
                    $split  = explode($references[$i], $string);
                    $string = substr($string, strlen($split[0].$references[$i]));

                    $items [] = [
                        'reference' => $references[$i],
                        'delimiter' => trim($split[0]),
                        'position'  => $i + 1
                    ];
                }
                else {
                    $items [] = [
                        'reference' => '',
                        'delimiter' => $string,
                        'position'  => $i + 1
                    ];
                }
            }
        }

        return $items;
    }


    static function FormatReference ($edition) {

        // Split everything
        $splitted = explode(' ', $edition);
        $first = $splitted[0];

        $first = explode('/', $first);
        if (is_numeric(substr($first[0], -1))) {
            $first[0] = substr($first[0], 0, -1).'<sup>'.substr($first[0], -1).'</sup>';
        }
        $first = implode('/', $first);

        // Join everything
        $splitted[0] = $first;
        $edition = implode(' ', $splitted);

        return $edition;
    }


    static function FormatQuote ($quote) {

        // Split everything
        $splitted = explode(' ', $quote);

        // first split is numeric
        if(is_numeric($splitted[0]) && isset($splitted[1])) {

            if (ctype_alpha($splitted[1])) {
                $splitted[1] = self::MakeCursiva($splitted[1]);
            }
        }

        // second split is numeric
        else if (isset($splitted[1]) && isset($splitted[2])) {

            if (is_numeric($splitted[1])) {
                if (ctype_alpha($splitted[2])) {
                    $splitted[2] = self::MakeCursiva($splitted[2]);
                }
            }
        }

        // Join everything
        $quote = implode(' ', $splitted);

        return $quote;
    }


    static function MakeCursiva ($string) {
        return '<i>'.$string.'</i>';
    }


    static function MakeSorting($edition, $quote) {

        if (empty($edition)) {$edition = 999;}

        $quote = preg_replace('/[^ \w]+/', '', $quote);

        $splitted = explode(' ', $quote);

        foreach($splitted as $i => $split) {
            if (is_numeric($split)) {
                $splitted[$i] = sprintf('%06d', $split);
            }
            else {
                $length = strlen($split);

                if($length < 6) {
                    $splitted[$i] = substr('aaaaaa', 0, (6 - $length)).$splitted[$i];
                }
                else {
                    $splitted[$i] = substr($split, 0, 6);
                }
            }
        }

        $quote = implode('_', $splitted);

        return substr(($edition.'_'.$quote), 0, 45);
    }


    static function RebuildName ($parsing) {

        $name_formated = implode(' ', array_map(function($v) {
            return
                ($v['position'] != 1 ? ($v['delimiter'].' ') : '')
                .
                (empty($v['start_clamped_by']) ? '' : $v['start_clamped_by'])
                .
                $v['ref_short']
                .
                (empty($v['end_clamped_by'])   ? '' : $v['end_clamped_by'])
            ;
        }, $parsing));

        /*$name_object = array_map(function($v) {
            return [
                'position'      =>  $v['position'],
                'delimiter'     =>  $v['delimiter'],
                'ref_short'     =>  $v['ref_short'],
                'ref_full'      =>  $v['ref_full'],
                'clamped'       =>  [
                    'start' => empty($v['start_clamped_by']) ? '' : $v['start_clamped_by'],
                    'end'   => empty($v['end_clamped_by'])   ? '' : $v['end_clamped_by']
                ]
            ];
        }, $parsing);*/

        $name_object = '[{'.implode('},{', array_map(function($v) {
            return
                '"position": '  .$v['position'].', '.
                '"delimiter": ' .'"'.str_replace('"', '\"', $v['delimiter']).'", '.
                '"ref_short": ' .'"'.str_replace('"', '\"', $v['ref_short']).'", '.
                '"ref_full": '  .'"'.str_replace('"', '\"', $v['ref_full']).'", '.
                '"clamped": {'.
                    '"start": ' .'"'.(empty($v['start_clamped_by']) ? '' : $v['start_clamped_by']).'", '.
                    '"end": '   .'"'.(empty($v['end_clamped_by'])   ? '' : $v['end_clamped_by']).'"'.
                '}'
            ;
        }, $parsing)).'}]';

        return [
            'sort_index'    =>  $parsing[0]['sort_by'],
            'name_formated' =>  $name_formated,
            'name_object'   =>  $name_object//json_encode($name_object),
        ];
    }


    static function MapAndTrim ($array) {

        return array_map(function($item) {
            return trim($item);
        }, $array);
    }


    static function GetEditions () {
        $ed = DB::table('cil_fm.web_editions')
            -> select([
                'id             AS id',
                'id_we          AS we',
                'abbreviation   AS a',
                'name_full      AS name',
                DB::RAW('CHAR_LENGTH( REPLACE ( abbreviation, \' \', \'__\') ) - CHAR_LENGTH(abbreviation) AS space_count')
            ])
            -> orderBy('space_count', 'DESC')
            -> orderBy('abbreviation', 'DESC')
            -> get();

        return json_decode($ed, TRUE);
    }

    static function GetInscriptions() {
        $dbi = DB::table('cil_fm.raw_'.self::$latest_import.'_inscriptions')
            -> select(['id', 'fm_id', 'name_plain'])
            -> get();

        return json_decode($dbi, TRUE);
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

    static function WriteErrorLog ($input) {
        echo("\n");
        $content = [
            'warnings' => [],
            'errors' => []
        ];

        foreach ($input as $row) {
            $content[$row[0] === true ? 'errors' : 'warnings'][] = '"'.implode('","', [
                $row[1]['fm_id'],
                str_replace('"', '\"', $row[1]['name_plain']),
                str_replace('"', '\"', $row[2])
            ]).'"';
        }

        foreach (['warnings', 'errors'] as $class) {
            if (!empty($content[$class])) {
                echo('WRITING '.count($content[$class]).' '.$class.' TO LOG FILE ... ');
                $header = ["Konkordanznummer,Name,Problem"];
                file_put_contents(self::$path.'processing_'.$class.'.csv', implode("\n", array_merge($header, $content[$class])));
                echo("SUCCESS \n");
            }
        }
    }


    // SQL FILE WRITER -----------------------------------------------------------------
    static function WriteBaseInscriptions ($content) {
        echo('WRITING INSCRIPTION FILES ... ');
        $table = self::$table_base_inscriptions;

        $sql =
            '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;'."\n".
            '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;'."\n".
            '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;'."\n".
            '/*!40101 SET NAMES utf8 */;'."\n".
            '/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;'."\n".
            '/*!40103 SET TIME_ZONE=\'+01:00\' */;'."\n".
            '/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;'."\n".
            '/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;'."\n".
            '/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */;'."\n".
            '/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;'."\n".
            "\n".
            '--'."\n".
            '-- Table structure for table `'.$table.'`'."\n".
            '--'."\n".
            "\n".
            'DROP TABLE IF EXISTS `'.$table.'`;'."\n".
            '/*!40101 SET @saved_cs_client     = @@character_set_client */;'."\n".
            '/*!40101 SET character_set_client = utf8 */;'."\n".

            // Create Table Statement
            'CREATE TABLE `'.$table.'` (
                `id` int NOT NULL,
                `concordance` char(9) NOT NULL,
                `name_plain` text NOT NULL,
                `name_formated` text,
                `name_object` text,
                `sort_index` varchar(45) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `concordance_UNIQUE` (`concordance`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'."\n".
            '/*!40101 SET character_set_client = @saved_cs_client */;'."\n".
            "\n".
            '--'."\n".
            '-- Dumping data for table `'.$table.'`'."\n".
            '--'."\n".
            "\n".
            'LOCK TABLES `'.$table.'` WRITE;'."\n".
            '/*!40000 ALTER TABLE `'.$table.'` DISABLE KEYS */;'."\n".

            // Insert Values
            'INSERT INTO `'.$table.'` VALUES '."\n".
            implode(',', $content)."\n".
            ';'."\n".
            '/*!40000 ALTER TABLE `'.$table.'` ENABLE KEYS */;'."\n".
            'UNLOCK TABLES;';

        file_put_contents(self::$file_base_inscriptions, $sql);
        echo("SUCCESS\n");
    }


    static function WriteSearchNames ($content) {
        echo('WRITING SERACHNAMES FILES ... ');
        $table = self::$table_search_names;

        $sql =
            '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;'."\n".
            '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;'."\n".
            '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;'."\n".
            '/*!40101 SET NAMES utf8 */;'."\n".
            '/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;'."\n".
            '/*!40103 SET TIME_ZONE=\'+01:00\' */;'."\n".
            '/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;'."\n".
            '/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;'."\n".
            '/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */;'."\n".
            '/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;'."\n".
            "\n".
            '--'."\n".
            '-- Table structure for table `'.$table.'`'."\n".
            '--'."\n".
            "\n".
            'DROP TABLE IF EXISTS `'.$table.'`;'."\n".
            '/*!40101 SET @saved_cs_client     = @@character_set_client */;'."\n".
            '/*!40101 SET character_set_client = utf8 */;'."\n".

            // Create Table Statement
            'CREATE TABLE `'.$table.'` (
                `id` int NOT NULL,
                `id_inscription` int NOT NULL,
                `position` int NOT NULL,
                `delimiter` varchar(45) DEFAULT NULL,
                `search_string` varchar(255) NOT NULL,
                `id_edition` int DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'."\n".
            '/*!40101 SET character_set_client = @saved_cs_client */;'."\n".
            "\n".
            '--'."\n".
            '-- Dumping data for table `'.$table.'`'."\n".
            '--'."\n".
            "\n".
            'LOCK TABLES `'.$table.'` WRITE;'."\n".
            '/*!40000 ALTER TABLE `'.$table.'` DISABLE KEYS */;'."\n".

            // Insert Values
            'INSERT INTO `'.$table.'` VALUES '."\n".
            implode(',', $content)."\n".
            ';'."\n".
            '/*!40000 ALTER TABLE `'.$table.'` ENABLE KEYS */;'."\n".
            'UNLOCK TABLES;';

        file_put_contents(self::$file_search_names, $sql);
        echo("SUCCESS\n");
    }


}
