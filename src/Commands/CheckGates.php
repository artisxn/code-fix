<?php

namespace codicastudio\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use codicastudio\LaravelMicroscope\SpyClasses\SpyGate;
use codicastudio\LaravelMicroscope\Traits\LogsErrors;

class CheckGates extends Command
{
    use LogsErrors;

    protected $signature = 'check:gates';

    protected $description = 'Checks the validity of gate definitions';

    public function handle(ErrorPrinter $errorPrinter)
    {
        event('microscope.start.command');
        $this->info('Checking gates...');

        $errorPrinter->printer = $this->output;

        $this->finishCommand($errorPrinter);
        $this->getOutput()->writeln(' - '.SpyGate::$definedGatesNum.' gate definitions were checked.');
        event('microscope.finished.checks', [$this]);

        return $errorPrinter->pended ? 1 : 0;
    }
}
