<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_raw_inscriptions extends Command
{
    protected $signature    = 'cil:raw_inscriptions';
    protected $description  = 'read raw CSVs of concordances/inscriptions';
    public function __construct() { parent::__construct(); }

    static $date = '210120';
    static $path = '/opt/projects/cil-laravel/sql/';
    static $table_base = 'raw';
    static $data = [
        'csv' => '/opt/projects/cil-laravel/fm/co.CSV',
        'table' => 'inscriptions'
    ];
    static $relations = [
        'fotos',
        'imprints',
        'scheden'
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

        // Parse CSV
        $parsed_csv = [];
        echo("\nPARSING - START\n");
        $parsed_csv = self::parse_csv(self::$data['csv'], ';');
        echo("\nPARSING - END\n\n");

        // Handle parsed CSV
        $records = [];
        echo("\nEXTRACTING - START\n");
        foreach ($parsed_csv AS $row) {
            // Inscriptions
            $record = [];
            $record['id'] = intval(substr($row['Konkordanznummer'], 2, 9));
            $record['fm_id'] = substr($row['Konkordanznummer'], 0, 9);
            $record['name_plain'] = trim($row['Inschriftnummer']);
            $record['edcs'] = trim($row['Clauss_ID_19']);
            // Last Edit
            $ea = explode('.', trim($row['Bearbeitet am']));
            $record['edited_at'] = $ea[2]."-".$ea[1]."-".$ea[0];

            echo("\t".$record['fm_id'].", ".$record['name_plain']."\n");
            $values = [];
            foreach ($record as $value) {
                $values[] = $value === null ? 'null' : ("'".trim(str_replace("'", "\\'", $value))."'");
            }
            $records['inscriptions'][] = '('.implode(',', $values).')';

            // Imprints
            for ($c = 1; $c <= 24; $c++) {
                $i = 'Abklatsch '.sprintf('%02d', $c);
                if (!empty($row[$i])) {
                    if(intval(substr($row[$i], 2, 9)) === 0) {
                        $errors[] = '"'.$record['fm_id'].'","'.str_replace('"', '\"', $i.": ".$row[$i]).'"';
                    } else {
                        $records['imprints'][] = '(null,'.$record['id'].','.intval(substr($row[$i], 2, 9)).')';
                    }
                }
            }
            // Fotos
            for ($c = 1; $c <= 15; $c++) {
                $i = 'Photothek '.sprintf('%02d', $c);
                if (!empty($row[$i])) {
                    $records['fotos'][] = '(null,'.$record['id'].','.intval(substr($row[$i], 2, 9)).')';
                }
            }
            // Scheden
            for ($c = 1; $c <= 21; $c++) {
                $i = 'Schede'.sprintf('%02d', $c);
                if (!empty($row[$i])) {
                    $records['scheden'][] = '(null,'.$record['id'].','.intval(substr($row[$i], 3, 10)).')';
                }
            }
        }
        echo("\nEXTRACTING - END\n");

        foreach (array_merge(['inscriptions'], self::$relations) as $entity) {
            echo("\nWRITING SQL FILE - START\n");
            self::write_sql_dump($entity, $records[$entity]);
            echo("\nWRITING SQL FILE - SUCCESS\n");
        }

        if (!empty($errors)) {
            $errors = implode("\n", $errors);

            file_put_contents('/opt/projects/cil-laravel/sql/imprint-errors.csv', $errors); //, FILE_APPEND
        }

        // Regular End of Script -------------------------------------------------------------------------------------
        foreach(array_merge(['inscriptions'], self::$relations) as $entity) {
            echo("\nNumber of ".strtoupper($entity).": ".count($records[$entity]));
        }
        echo("\n\nExecution time: ".(date('U') - $time)." sec\n");
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }

    static function parse_csv ($filename='', $delimiter=',') {
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
                    if (
                        substr($new_row['Konkordanznummer'], 0, 2) === 'KO' &&
                        !empty($new_row['Inschriftnummer']) && (
                            !empty($new_row['Schede01']) ||
                            !empty($new_row['Abklatsch 01']) ||
                            !empty($new_row['Photothek 01'])
                        )
                    ) {
                        $data[] = $new_row;
                        echo('1');
                    }
                    else {
                        echo('0');
                    }
                }
            }
            fclose($handle);
            echo("\nFINISHED PARSING\n");
        }
        return $data;
    }

    static function write_sql_dump ($entity, $content) {
        $name = $entity === 'inscriptions' ? 'inscriptions' : 'inscriptions_to_'.$entity;
        $table = self::$table_base.'_'.self::$date.'_'.$name;
        $file = self::$path.$name.'.sql';
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
            'CREATE TABLE `'.$table.'` ('."\n";
        if ($entity === 'inscriptions') {
            $sql .= "\n".
                "\t".'`id` int NOT NULL,'."\n".
                "\t".'`fm_id` char(9) NOT NULL,'."\n".
                "\t".'`name_plain` TEXT,'."\n".
                "\t".'`edcs_id` varchar(255) NOT NULL,'."\n".
                "\t".'`edited_at` DATE,';
        } else {
            $sql .= "\n".
                "\t".'`id` int NOT NULL AUTO_INCREMENT,'."\n".
                "\t".'`id_inscription` int NOT NULL,'."\n".
                "\t".'`id_'.substr($entity, 0, -1).'` int NOT NULL,';
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
