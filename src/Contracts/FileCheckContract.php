<?php

namespace codicastudio\LaravelMicroscope\Contracts;

interface FileCheckContract
{
    /**
     * Called when working on a file.
     *
     * @param $file
     *
     * @return mixed
     */
    public function onFileTap($file);
}
