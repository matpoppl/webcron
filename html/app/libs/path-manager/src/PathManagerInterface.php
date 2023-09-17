<?php

namespace matpoppl\PathManager;

interface PathManagerInterface
{
    public function getPathname(string $location) : string;
}
