<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_create_data extends Command
{
    protected $signature    = 'cil:create_data';
    protected $description  = 'Write Main SQL Table';
    public function __construct() { parent::__construct(); }


    // -----------------------------------------------------------------------
    static $latest_import   = 210902;
    static $table           = 'web_inscriptions';
    static $file            = '/opt/projects/cil-laravel/output/web_inscriptions.sql';

    // -----------------------------------------------------------------------
    public function handle() {
        $time = date('U');
        $data = [];

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "---------------- CIL INSCRIPTION IMPORTER ----------------\n".
            "----------------------------------------------------------\n\n"
        );

        // Base
        echo("\n".'Running Base Query ... ');
        $dbi = DB::table('cil_fm.web_base_inscriptions AS b')
            -> leftJoin('cil_fm.raw_'.self::$latest_import.'_inscriptions                   AS ins',    'ins.id',               '=', 'b.id')
            -> select([
                'b.id AS id',
                'b.concordance AS concordance',
                'b.name_plain AS name_plain',
                'b.name_formated AS name_formated',
                'b.name_object AS name_object',
                'b.sort_index AS sort_index',
                // EDCS
                'ins.edcs_id AS edcs',
            ])
            -> groupBy('b.id')
            -> get();

        $data['inscriptions'] = json_decode($dbi, TRUE);
        echo('SUCCESS');

        // Fotos
        echo("\n".'Running Foto Query ... ');
        $dbi = DB::table('cil_fm.web_base_inscriptions AS b')
            -> leftJoin('cil_fm.raw_'.self::$latest_import.'_inscriptions_to_fotos          AS itf',    'itf.id_inscription',   '=', 'b.id')
            -> leftJoin('cil_fm.raw_'.self::$latest_import.'_fotos                          AS f',      'f.id',                 '=', 'itf.id_foto')
            -> select([
                'b.id AS id',
                DB::Raw('IF(GROUP_CONCAT(f.id) > "", json_arrayagg( json_object(
                    "id",        	f.id,
                    "fmid",			f.fm_id,
                    "is_public",	f.is_public,
                    "author",     	f.author,
                    "year",       	f.year
                )), null) AS fotos'),
            ])
            -> groupBy('b.id')
            -> get();

        $data['fotos'] = json_decode($dbi, TRUE);
        echo('SUCCESS');

        // Base
        echo("\n".'Running Imprint Query ... ');
        $dbi = DB::table('cil_fm.web_base_inscriptions AS b')
            -> leftJoin('cil_fm.raw_'.self::$latest_import.'_inscriptions_to_imprints       AS iti',    'iti.id_inscription',   '=', 'b.id')
            -> leftJoin('cil_fm.raw_'.self::$latest_import.'_imprints                       AS i',      'i.id',                 '=', 'iti.id_imprint')
            -> select([
                'b.id AS id',
                DB::Raw('IF(GROUP_CONCAT(i.id) > "", json_arrayagg( json_object(
                    "id",     	i.id,
                    "fmid",		i.fm_id,
                    "number",	i.number,
                    "kind",		i.kind,
                    "link",		i.scan_3d
                )), null) AS imprints'),
                DB::RAW('if(count(i.scan_3d) > 0, 1, null) AS imprints_3d')
            ])
            -> groupBy('b.id')
            -> get();

        $data['imprints'] = json_decode($dbi, TRUE);
        echo('SUCCESS');

        // Base
        echo("\n".'Running Scheden Query ... ');
        $dbi = DB::table('cil_fm.web_base_inscriptions AS b')
            -> leftJoin('cil_fm.raw_'.self::$latest_import.'_inscriptions_to_scheden        AS its',    'its.id_inscription',   '=', 'b.id')
            -> leftJoin('cil_fm.raw_'.self::$latest_import.'_scheden                        AS s',      's.id',                 '=', 'its.id_schede')
            -> select([
                'b.id AS id',
                DB::Raw('IF(GROUP_CONCAT(s.id) > "", json_arrayagg( json_object(
                    "id",    	s.id,
                    "fmid",     s.fm_id
                )), null) AS scheden')
            ])
            -> groupBy('b.id')
            -> get();

        $data['scheden'] = json_decode($dbi, TRUE);
        echo('SUCCESS');

            /*-> leftJoin('cil_fm.raw_'.self::$latest_import.'_inscriptions                   AS ins',    'ins.id',               '=', 'b.id')
            //-> leftJoin('cil_fm.raw_'.self::$latest_import.'_inscriptions_to_fotos          AS itf',    'itf.id_inscription',   '=', 'b.id')
            //-> leftJoin('cil_fm.raw_'.self::$latest_import.'_fotos                          AS f',      'f.id',                 '=', 'itf.id_foto')
            //-> leftJoin('cil_fm.raw_'.self::$latest_import.'_inscriptions_to_imprints       AS iti',    'iti.id_inscription',   '=', 'b.id')
            //-> leftJoin('cil_fm.raw_'.self::$latest_import.'_imprints                       AS i',      'i.id',                 '=', 'iti.id_imprint')
            //-> leftJoin('cil_fm.raw_'.self::$latest_import.'_inscriptions_to_scheden        AS its',    'its.id_inscription',   '=', 'b.id')
            //-> leftJoin('cil_fm.raw_'.self::$latest_import.'_scheden                        AS s',      's.id',                 '=', 'its.id_schede')
            -> select([
                'b.id AS id',
                'b.concordance AS concordance',
                'b.name_plain AS name_plain',
                'b.name_formated AS name_formated',
                'b.name_object AS name_object',
                'b.sort_index AS sort_index',
                // EDCS
                'ins.edcs_id AS edcs',
                // Fotos
                DB::Raw('(SELECT json_arrayagg(
                    json_object(
                        "id",        	f.id,
                        "fmid",			f.fm_id,
                        "is_public",	f.is_public,
                        "author",     	f.author,
                        "year",       	f.year
                    ))
                    FROM cil_fm.raw_'.self::$latest_import.'_inscriptions_to_fotos  AS itf
                    LEFT JOIN cil_fm.raw_'.self::$latest_import.'_fotos             AS f    ON f.id = itf.id_foto
                    WHERE itf.id_inscription = b.id
                ) AS fotos'),
                // Imprints
                DB::Raw('(SELECT json_arrayagg(
                    json_object(
                        "id",     	i.id,
                        "fmid",		i.fm_id,
                        "number",	i.number,
                        "kind",		i.kind,
                        "link",		i.scan_3d
                    ))
                    FROM cil_fm.raw_'.self::$latest_import.'_inscriptions_to_imprints   AS iti
                    LEFT JOIN cil_fm.raw_'.self::$latest_import.'_imprints              AS i    ON i.id = iti.id_imprint
                    WHERE itf.id_inscription = b.id
                ) AS imprints'),
                // Imprints 3D
                DB::RAW('(SELECT
                    if(count(i.scan_3d) > 0, 1, null) AS cnt
                    FROM cil_fm.raw_'.self::$latest_import.'_inscriptions_to_imprints   AS iti
                    LEFT JOIN cil_fm.raw_'.self::$latest_import.'_imprints              AS i    ON i.id = iti.id_imprint
                    WHERE itf.id_inscription = b.id
                ) AS imprints_3d'),
                // Scheden
                DB::Raw('(SELECT json_arrayagg(
                    json_object(
                        "id",    	s.id,
                        "fmid",     s.fm_id
                    ))
                    FROM cil_fm.raw_'.self::$latest_import.'_inscriptions_to_scheden   AS its
                    LEFT JOIN cil_fm.raw_'.self::$latest_import.'_scheden              AS s    ON s.id = its.id_schede
                    WHERE itf.id_inscription = b.id
                ) AS scheden')
            ])
            //-> groupBy('b.id')
            -> get();*/

        echo("\n\n".'Records received: '.count($data['inscriptions'])."\n");
        self::WriteSQL($data);
        echo("\n\nTotal execution time: ".(date('U') - $time)." sec\n");


        // Regular End of Script -------------------------------------------------------------------------------------
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }

    // SQL FILE WRITER -----------------------------------------------------------------
    static function WriteSQL ($input) {
        // Inscriptions
        echo("\n".'Iterating Inscriptions ... ');
        foreach ($input['inscriptions'] as $row) {
            $data[$row['id']] = [
                'id'                => $row['id'],
                'concordance'       => "'".$row['concordance']."'",
                'name_plain'        => "'".str_replace("'", "\'", $row['name_plain'])."'",
                'name_formated'     => "'".str_replace("'", "\'", $row['name_formated'])."'",
                'name_object'       => "'".str_replace("'", "\'", $row['name_object'])."'",
                'sort_index'        => "'".str_replace("'", "\'", $row['sort_index'])."'",
                'edcs'              => "'".str_replace("'", "\'", $row['edcs'])."'",
            ];
        }
        echo('SUCCESS');
        // Fotos
        echo("\n".'Iterating Fotos ... ');
        foreach ($input['fotos'] as $row) {
            $data[$row['id']]['fotos'] = empty($row['fotos']) ? 'null' : "'".str_replace("'", "\'", $row['fotos'])."'";
        }
        echo('SUCCESS');
        // Imprints
        echo("\n".'Iterating Imprints ... ');
        foreach ($input['imprints'] as $row) {
            $data[$row['id']]['imprints'] = empty($row['imprints']) ? 'null' : "'".str_replace("'", "\'", $row['imprints'])."'";
            $data[$row['id']]['imprints_3d'] = empty($row['imprints_3d']) ? 0 : 1;
        }
        echo('SUCCESS');
        // Scheden
        echo("\n".'Iterating Scheden ... ');
        foreach ($input['scheden'] as $row) {
            $data[$row['id']]['scheden'] = empty($row['scheden']) ? 'null' : "'".str_replace("'", "\'", $row['scheden'])."'";
        }
        echo('SUCCESS');

        // Imploding
        echo("\n".'Imploding Rows ... ');
        foreach ($data as $row) {
            $content[] = '('.implode(',', $row).')';
        }
        echo('SUCCESS'."\n");

        /*foreach ($input['inscriptions'] as $row) {
            $id = $row['id'];
            $new_row = [
                $input['inscriptions']$id['id']
                concordance
                name_plain
                name_formated
                name_object
                sort_index
                edcs
                fotos
                imprints
                imprints_3d
                scheden
            ];
            foreach ($row as $key => $value) {
                if ($key === 'id' || $value === 0 || $value === 1) {
                    $new_row[] = $value;
                }
                else if ($value === null) {
                    $new_row[] = 'null';
                }
                else {
                    $new_row[] = "'".str_replace("'", "\'", $value)."'";
                }
            }
            $content[] = '('.implode(',', $new_row).')';
        }*/

        echo('WRITING SQL FILE ... ');
        $table = self::$table;

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
                "\t".'`id` int NOT NULL,'."\n".
                "\t".'`concordance` char(9) NOT NULL,'."\n".
                "\t".'`name_plain` text NOT NULL,'."\n".
                "\t".'`name_formated` text,'."\n".
                "\t".'`name_object` text,'."\n".
                "\t".'`sort_index` varchar(45) DEFAULT NULL,'."\n".
                "\t".'`edcs` varchar(255) DEFAULT NULL,'."\n".
                "\t".'`fotos` text,'."\n".
                "\t".'`imprints` text,'."\n".
                "\t".'`imprints_3d` tinyint,'."\n".
                "\t".'`scheden` text,'."\n".
                "\t".'PRIMARY KEY (`id`),'."\n".
                "\t".'UNIQUE KEY `concordance_UNIQUE` (`concordance`)'."\n".
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
            implode(','."\n", $content).';'."\n".
            '/*!40000 ALTER TABLE `'.$table.'` ENABLE KEYS */;'."\n".
            'UNLOCK TABLES;';

        file_put_contents(self::$file, $sql);
        echo("SUCCESS\n");
    }
}
