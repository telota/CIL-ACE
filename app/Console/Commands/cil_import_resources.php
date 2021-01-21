<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_import_resources extends Command
{
    protected $signature    = 'cil:import_resources';
    protected $description  = 'Process CIL FM Export and write to MySQL-DB';
    public function __construct() { parent::__construct(); }


    // -----------------------------------------------------------------------


    // -----------------------------------------------------------------------
    public function handle()
    {
        $time = date('U');

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "----------------- CIL RESOURCES IMPORTER -----------------\n".
            "----------------------------------------------------------\n\n"
        );

        echo("\nexecution time: ".(date('U') - $time)." sec\n");


        // Regular End of Script -------------------------------------------------------------------------------------
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }
}
