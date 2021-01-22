<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_raw_resources extends Command {
    protected $signature    = 'cil:raw_resources';
    protected $description  = 'read raw CSVs of Fotos, Imprints and Scheden';
    public function __construct() { parent::__construct(); }

    static $date = '210120';
    static $path = '/opt/projects/cil-laravel/sql/';
    static $table_base = 'raw';
    static $resources = [
        // Fotos
        'fo' => [
            'csv' => '/opt/projects/cil-laravel/fm/fo.CSV',
            'table' => 'fotos'
        ],
        // Abklatsche
        'im' => [
            'csv' => '/opt/projects/cil-laravel/fm/im.CSV',
            'table' => 'imprints'
        ],
        // Scheden
        'sc' => [
            'csv' => '/opt/projects/cil-laravel/fm/sc.CSV',
            'table' => 'scheden'
        ]
    ];

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

        foreach(array_keys(self::$resources) as $entity) {
            // Parse CSV
            $parsed_csv = [];
            echo("\nPARSING ".strtoupper($entity)." - START\n");
            $parsed_csv = self::parse_csv($entity, ';');
            echo("\nPARSING ".strtoupper($entity)." - END\n\n");

            // Handle parsed CSV
            $records[$entity] = [];
            echo("\nPREPARING ".strtoupper($entity)." - START\n");
            foreach ($parsed_csv AS $row) {
                $values = [];
                foreach (self::{'handle_'.$entity}($row) as $value) {
                    $values[] = $value === null ? 'null' : ("'".trim(str_replace("'", "\\'", $value))."'");
                }
                $records[$entity][] = '('.implode(',', $values).')';
            }
            echo("\nPREPARING ".strtoupper($entity)." - END\n");
            echo("Number of records: ".count($records[$entity])."\n");
            echo("\nWRITING SQL FILE - START\n");
            self::write_sql_dump($entity, $records[$entity]);
            echo("\nWRITING SQL FILE - SUCCESS\n");
        }

        // Regular End of Script -------------------------------------------------------------------------------------
        foreach(array_keys(self::$resources) as $entity) {
            echo("\nNumber of records in ".strtoupper($entity).": ".count($records[$entity]));
        }
        echo("\n\nExecution time: ".(date('U') - $time)." sec\n");
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }

    static function parse_csv ($entity, $delimiter=',') {
        $filename = self::$resources[$entity]['csv'];
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
                    if (self::validate_new_row($entity, $new_row)) {
                        echo('.');
                        $data[] = $new_row;
                    }
                }
            }
            fclose($handle);
            echo("\nFINISHED PARSING\n");
        }
        return $data;
    }

    static function validate_new_row($entity, $row) {
        if ($entity === 'fo') {
            return substr($row['CIL_Inventar'], 0, 2) === 'PH' ? true : false;
        }
        else if ($entity === 'im') {
            return substr($row['Abklatschnummer'], 0, 2) === 'EC' ? true : false;
        }
        else if ($entity === 'sc') {
            return substr($row['Scheden_Nummer'], 0, 3) === 'SCH' ? true : false;
        }
        else {
            die('ERROR: '.$entity.' is not declared in New Row Validation');
        }
    }

    // Imprints
    static function handle_im ($row) {
        $record = [];
        $record['id']       = intval(substr($row['Abklatschnummer'], 2, 9));
        $record['fm_id']    = substr($row['Abklatschnummer'], 0, 9);
        $record['number']   = empty($row['Abklatsch Signatur']) ? null : trim($row['Abklatsch Signatur']);
        $record['kind']     = empty($row['Art']) ? null : trim($row['Art']);
        $record['scan_3d']  = empty($row['Scann_3D 01']) ? null : trim($row['Scann_3D 01']);

        echo("\t".$record['fm_id'].", ".$record['number'].", ".$record['scan_3d']."\n");
        return $record;
    }

    // Fotos
    static function handle_fo ($row) {
        $record = [];
        $record['id']       = intval(substr($row['CIL_Inventar'], 2, 9));
        $record['fm_id']    = substr($row['CIL_Inventar'], 0, 9);
        $record['author']   = empty($row['Photograph']) || $row['Photograph'] === 'Unbekannt' ? null : trim($row['Photograph']);
        $record['year']     = empty($row['AUFNAHME_JAHR']) || $row['AUFNAHME_JAHR'] === '5555' ? null : intval(trim($row['AUFNAHME_JAHR']));
        $record['is_public']   = $row['Nicht_Internet'] === 'Nicht Internet' ? 0 : 1;

        echo("\t".$record['fm_id'].", ".$record['author']." - ".$record['year']."\n");
        return $record;
    }

    // Scheden
    static function handle_sc ($row) {
        $record = [];
        $record['id']       = intval(substr($row['Scheden_Nummer'], 3, 10));
        $record['fm_id']    = substr($row['Scheden_Nummer'], 0, 10);

        echo("\t".$record['fm_id']."\n");
        return $record;
    }

    static function write_sql_dump ($entity, $content) {
        $table = self::$table_base.'_'.self::$date.'_'.self::$resources[$entity]['table'];
        $file = self::$path.self::$resources[$entity]['table'].'.sql';
        echo("\t".$file);

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
            'CREATE TABLE `'.$table.'` ('."\n".
                "\t".'`id` int NOT NULL,';
        if ($entity === 'fo') {
            $sql .= "\n".
                "\t".'`fm_id` char(9) NOT NULL,'."\n".
                "\t".'`author` varchar(255) DEFAULT NULL,'."\n".
                "\t".'`year` int DEFAULT NULL,'."\n".
                "\t".'`is_public` tinyint DEFAULT NULL,';
        } else if ($entity === 'im') {
            $sql .= "\n".
                "\t".'`fm_id` char(9) NOT NULL,'."\n".
                "\t".'`number` varchar(255) DEFAULT NULL,'."\n".
                "\t".'`kind` varchar(45) DEFAULT NULL,'."\n".
                "\t".'`scan_3d` varchar(255) DEFAULT NULL,';
        } else if ($entity === 'sc') {
            $sql .= "\n".
                "\t".'`fm_id` char(10) NOT NULL,';
        }

        $sql .= "\n".
                 "\t".'PRIMARY KEY (`id`)'."\n".
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'."\n".
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

        file_put_contents($file.'.sql', $sql);
    }
}
