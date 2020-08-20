<?php

namespace codicastudio\LaravelMicroscope\Checks;

use Illuminate\Support\Str;
use codicastudio\LaravelMicroscope\Analyzers\ComposerJson;
use codicastudio\LaravelMicroscope\Analyzers\NamespaceCorrector;
use codicastudio\LaravelMicroscope\Analyzers\ReplaceLine;
use codicastudio\LaravelMicroscope\CheckClasses;
use codicastudio\LaravelMicroscope\ErrorReporters\ErrorPrinter;

class CheckStringy
{
    public static function check($tokens, $absFilePath)
    {
        (new self())->checkStringy($tokens, $absFilePath);
    }

    public function checkStringy($tokens, $absFilePath)
    {
        $psr4 = ComposerJson::readAutoload();
        $namespaces = array_keys($psr4);
        $errorPrinter = resolve(ErrorPrinter::class);
        foreach ($tokens as $token) {
            if (! $this->isPossiblyClassyString($token, $namespaces)) {
                continue;
            }
            $classPath = \trim($token[1], '\'\"');
            if (CheckClasses::isAbsent($classPath)) {
                $relPath = NamespaceCorrector::getRelativePathFromNamespace($classPath);
                // Is a correct namespace path, pointing to a directory
                if (is_dir(base_path($relPath))) {
                    continue;
                }
                $errorPrinter->wrongUsedClassError($absFilePath, $token[1], $token[2]);
                continue;
            }

            $errorPrinter->printLink($absFilePath, $token[2]);
            $command = app('current.command');
            $command->getOutput()->text($token[2].' |'.file($absFilePath)[$token[2] - 1]);
            $answer = $command->getOutput()->confirm('Do you want to replace: '.$token[1].' with ::class version of it? ', true);
            if ($answer) {
                dump('Replacing: '.$token[1].'  with: '.$this->getClassyPath($classPath));
                ReplaceLine::replaceFirst($absFilePath, $token[1], $this->getClassyPath($classPath));
                $command->info('====================================');
            }
        }
    }

    protected function getClassyPath($string)
    {
        ($string[0] !== '\\') && ($string = '\\'.$string);
        $string .= '::class';

        return $string;
    }

    private function isPossiblyClassyString($token, $namespaces)
    {
        $chars = ['@', ' ', ',', ':', '/', '.', '-'];

        return $token[0] == T_CONSTANT_ENCAPSED_STRING && Str::contains($token[1], $namespaces) && ! Str::contains($token[1], $chars);
    }
}
