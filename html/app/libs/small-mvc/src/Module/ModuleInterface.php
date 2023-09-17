<?php

namespace matpoppl\SmallMVC\Module;

use matpoppl\SmallMVC\Application;

interface ModuleInterface
{
    public function init(Application $app);
    public function getConfig() : array;
}
