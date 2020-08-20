<?php

namespace codicastudio\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use codicastudio\LaravelMicroscope\Analyzers\ComposerJson;
use codicastudio\LaravelMicroscope\Analyzers\FilePath;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use codicastudio\LaravelMicroscope\GenerateCode;

class CheckExpansions extends Command
{
    protected $signature = 'check:generate';

    protected $description = 'Generates code';

    public function handle(ErrorPrinter $errorPrinter)
    {
        $this->info('Scanning for Empty Provider Files');
        $errorPrinter->printer = $this->output;

        $autoload = ComposerJson::readAutoload();
        foreach ($autoload as $psr4Namespace => $psr4Path) {
            $files = FilePath::getAllPhpFiles($psr4Path);
            GenerateCode::serviceProvider($files, $psr4Path, $psr4Namespace, $this);
        }
    }
}
