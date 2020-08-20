<?php

namespace codicastudio\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use codicastudio\LaravelMicroscope\Checks\ActionsComments;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use codicastudio\LaravelMicroscope\Psr4Classes;
use codicastudio\LaravelMicroscope\Traits\LogsErrors;

class CheckActionComments extends Command
{
    use LogsErrors;

    protected $signature = 'check:action_comments';

    protected $description = 'Adds route definition to the controller actions.';

    public function handle(ErrorPrinter $errorPrinter)
    {
        $errorPrinter->printer = $this->output;

        $this->info('Commentify Route Actions...');

        ActionsComments::$command = $this;

        Psr4Classes::check([ActionsComments::class]);

        $this->finishCommand($errorPrinter);

        return $errorPrinter->hasErrors() ? 1 : 0;
    }
}
