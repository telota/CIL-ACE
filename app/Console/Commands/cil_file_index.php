<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_file_index extends Command {
    protected $signature    = 'cil:file_index';
    protected $description  = 'read content of dir';
    public function __construct() { parent::__construct(); }

    static $dir = '/opt/projects/cil/silo';
    static $file = '/opt/projects/cil/output/';

    // -----------------------------------------------------------------------
    public function handle() {
        $time = date('U');

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------------- START --------------------------\n".
            "----------------------------------------------------------\n\n"
        );

        echo "Scanning directory ... ";
        $files = scandir(self::$dir);
        $files = array_diff($files, ['..', '.']);
        echo count($files).' files'."\n";
        $files = json_encode($files, JSON_UNESCAPED_UNICODE);

        echo "Writing File ... ";
        file_put_contents(self::$file.'index_'.date('Y-m-d').'.json', $files);
        echo 'SUCCESS'."\n";

        echo("\n\nExecution time: ".(date('U') - $time)." sec\n");
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }
}
