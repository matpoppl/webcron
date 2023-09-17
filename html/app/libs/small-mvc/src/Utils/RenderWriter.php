<?php

namespace matpoppl\SmallMVC\Utils;

class RenderWriter implements LogWriterInterface
{
    public function getContents()
    {
    }

    public function write(array $logs)
    {
        echo "\n\n".'<br a="" /><pre style="font:400 14px/1.2 monospace;color:#999;background:#222;border:1px solid #f00;text-align:left;padding:0.5em 1em;margin:1px;">';
        echo implode("\n", $logs);
        echo "</pre>\n\n";
    }
}
