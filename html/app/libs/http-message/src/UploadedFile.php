<?php

namespace matpoppl\HttpMessage;

use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    private $name;
    private $type;
    private $tmp_name;
    private $error;
    private $size;
    private $moved = false;
    
    public function __construct($name, $type, $tmp_name, $error, $size)
    {
        $this->name = $name;
        $this->type = $type;
        $this->tmp_name = $tmp_name;
        $this->error = $error;
        $this->size = $size;
    }
    
    public function getError()
    {
        return $this->error;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getClientFilename()
    {
        return $this->name;
    }

    public function getStream()
    {
        if ($this->moved) {
            throw new \RuntimeException('UploadedFile already moved');
        }
        
        return InMemoryStream::fromResource(fopen($this->tmp_name, 'r'));
    }

    public function getClientMediaType()
    {
        return $this->type;
    }

    public function moveTo($targetPath)
    {
        $this->moved = true;
        return move_uploaded_file($this->tmp_name, $targetPath);
    }
}
