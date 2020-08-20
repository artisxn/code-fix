<?php

namespace codicastudio\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use codicastudio\LaravelMicroscope\BladeFiles;
use codicastudio\LaravelMicroscope\Checks\CheckIsQuery;
use codicastudio\LaravelMicroscope\Contracts\FileCheckContract;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use codicastudio\LaravelMicroscope\Traits\LogsErrors;
use codicastudio\LaravelMicroscope\Traits\ScansFiles;

class CheckBladeQueries extends Command implements FileCheckContract
{
    use LogsErrors;
    use ScansFiles;

    protected $signature = 'check:blade_queries {--d|detailed : Show files being checked}';

    protected $description = 'Checks db queries in blade files';

    public function handle(ErrorPrinter $errorPrinter)
    {
        event('microscope.start.command');
        $this->info('Checking blade files for db queries...');

        $errorPrinter->printer = $this->output;

        // checks the blade files for database queries.
        BladeFiles::check([CheckIsQuery::class]);

        $this->finishCommand($errorPrinter);
        $errorPrinter->printTime();

        return $errorPrinter->hasErrors() ? 1 : 0;
    }
}
