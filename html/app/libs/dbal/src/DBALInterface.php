<?php

namespace matpoppl\DBAL;

interface DBALInterface extends DriverInterface
{
    public function getOption($name, $default = null);
}
