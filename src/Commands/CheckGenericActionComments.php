<?php

namespace codicastudio\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use codicastudio\LaravelMicroscope\Checks\ActionsUnDocblock;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use codicastudio\LaravelMicroscope\Psr4Classes;
use codicastudio\LaravelMicroscope\Traits\LogsErrors;

class CheckGenericActionComments extends Command
{
    use LogsErrors;

    protected $signature = 'check:docblocks';

    protected $description = 'removes generic docblocks from controllers.';

    public function handle(ErrorPrinter $errorPrinter)
    {
        $errorPrinter->printer = $this->output;

        $this->info('removing generic docblocks...');

        ActionsUnDocblock::$command = $this;

        Psr4Classes::check([ActionsUnDocblock::class]);

        $this->finishCommand($errorPrinter);

        return $errorPrinter->hasErrors() ? 1 : 0;
    }
}
