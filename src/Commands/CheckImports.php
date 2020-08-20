<?php

namespace codicastudio\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use codicastudio\LaravelMicroscope\BladeFiles;
use codicastudio\LaravelMicroscope\CheckClasses;
use codicastudio\LaravelMicroscope\Checks\CheckClassReferences;
use codicastudio\LaravelMicroscope\Contracts\FileCheckContract;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use codicastudio\LaravelMicroscope\FileReaders\Paths;
use codicastudio\LaravelMicroscope\LaravelPaths\LaravelPaths;
use codicastudio\LaravelMicroscope\Psr4Classes;
use codicastudio\LaravelMicroscope\SpyClasses\RoutePaths;
use codicastudio\LaravelMicroscope\Traits\LogsErrors;
use codicastudio\LaravelMicroscope\Traits\ScansFiles;

class CheckImports extends Command implements FileCheckContract
{
    use LogsErrors;
    use ScansFiles;

    protected $signature = 'check:imports {--d|detailed : Show files being checked}';

    protected $description = 'Checks the validity of use statements';

    public function handle(ErrorPrinter $errorPrinter)
    {
        event('microscope.start.command');
        $this->info('Checking imports...');

        $errorPrinter->printer = $this->output;

        $this->checkFilePaths(RoutePaths::get());
        $this->checkFilePaths(Paths::getAbsFilePaths(app()->configPath()));
        $this->checkFilePaths(Paths::getAbsFilePaths(app()->databasePath('seeds')));
        $this->checkFilePaths(Paths::getAbsFilePaths(LaravelPaths::migrationDirs()));
        $this->checkFilePaths(Paths::getAbsFilePaths(LaravelPaths::factoryDirs()));

        Psr4Classes::check([CheckClasses::class]);

        // checks the blade files for class references.
        BladeFiles::check([CheckClassReferences::class]);

        $this->finishCommand($errorPrinter);
        $this->getOutput()->writeln(' - '.CheckClassReferences::$refCount.' class references were checked within: '.Psr4Classes::$checkedFilesNum.' classes and '.BladeFiles::$checkedFilesNum.' blade files');

        $errorPrinter->printTime();

        return $errorPrinter->hasErrors() ? 1 : 0;
    }

    private function checkFilePaths($paths)
    {
        foreach ($paths as $path) {
            $tokens = token_get_all(file_get_contents($path));
            CheckClassReferences::check($tokens, $path);
            CheckClasses::checkAtSignStrings($tokens, $path, true);
        }
    }
}
