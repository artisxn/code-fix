<?php

namespace codicastudio\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use codicastudio\LaravelMicroscope\Checks\RoutelessActions;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use codicastudio\LaravelMicroscope\Psr4Classes;
use codicastudio\LaravelMicroscope\Traits\LogsErrors;

class CheckDeadControllers extends Command
{
    use LogsErrors;

    protected $signature = 'check:dead_controllers';

    protected $description = 'Checks public controller methods to have routes.';

    public function handle(ErrorPrinter $errorPrinter)
    {
        event('microscope.start.command');
        $this->info('Checking for route-less controllers...');

        $errorPrinter->printer = $this->output;

        // checks calls like this: route('admin.user')
        // in the psr-4 loaded classes.
        Psr4Classes::check([RoutelessActions::class]);

        $this->finishCommand($errorPrinter);
        $errorPrinter->printTime();

        return $errorPrinter->hasErrors() ? 1 : 0;
    }
}
