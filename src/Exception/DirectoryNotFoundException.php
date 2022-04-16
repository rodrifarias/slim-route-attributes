<?php

namespace Rodrifarias\SlimRouteAttributes\Exception;

use Exception;

class DirectoryNotFoundException extends Exception
{
    public function __construct(string $path)
    {
        parent::__construct('Directory [' . $path . '] not found');
    }
}
