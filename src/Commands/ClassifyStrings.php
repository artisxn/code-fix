<?php

namespace codicastudio\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use codicastudio\LaravelMicroscope\Checks\CheckStringy;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use codicastudio\LaravelMicroscope\Psr4Classes;
use codicastudio\LaravelMicroscope\Traits\LogsErrors;

class ClassifyStrings extends Command
{
    use LogsErrors;

    protected $signature = 'check:stringy_classes';

    protected $description = 'Replaces string references with ::class version of them.';

    public function handle(ErrorPrinter $errorPrinter)
    {
        $this->info('Checking strings...');
        app()->singleton('current.command', function () {
            return $this;
        });
        $errorPrinter->printer = $this->output;
        Psr4Classes::check([CheckStringy::class]);

        $this->finishCommand($errorPrinter);

        return $errorPrinter->hasErrors() ? 1 : 0;
    }
}
