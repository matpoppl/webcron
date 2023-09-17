<?php

namespace matpoppl\HttpMessage;

use Psr\Http\Message\StreamInterface;

class InMemoryStream implements StreamInterface
{
    /** @var resource */
    private $stream;
    /** @var int */
    private $length = 0;

    public function isReadable()
    {
        return true;
    }

    public function getMetadata($key = null)
    {
        $data = stream_get_meta_data($this->stream);
        
        if (! is_array($data)) {
            throw new \UnexpectedValueException('Metadata read error');
        }
        
        if (null === $key) {
            return $data;
        }
        
        if (! array_key_exists($key, $data)) {
            throw new \DomainException("Meta field `{$key}` not found");
        }
        
        return $data[$key];
    }

    public function isSeekable()
    {
        return true;
    }

    public function read($length)
    {
        return fread($this->stream, $length);
    }

    public function tell()
    {
        return ftell($this->stream);
    }

    public function isWritable()
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->stream, $offset, $whence);
    }

    public function getSize()
    {
        return $this->length;
    }

    public function rewind()
    {
        rewind($this->stream);
        return $this;
    }

    public function getContents()
    {
        return stream_get_contents($this->stream);
    }

    public function close()
    {
        return fclose($this->stream);
    }

    public function eof()
    {
        return feof($this->stream);
    }

    public function write($string)
    {
        $this->length += fwrite($this->stream, $string);
        return $this;
    }

    public function detach()
    {}

    public function __toString()
    {
        $ret = $this->getContents();
        $this->rewind();
        return $ret;
    }
    
    /**
     * 
     * @param string $contents
     * @return \matpoppl\HttpMessage\InMemoryStream
     */
    public static function fromString(string $contents)
    {
        $ret = new static();
        $ret->stream = fopen('php://memory', 'rw');
        $ret->write($contents)->rewind();
        return $ret;
    }
    
    /**
     * 
     * @param resource $resource
     * @return \matpoppl\HttpMessage\InMemoryStream
     */
    public static function fromResource($resource)
    {
        if (! is_resource($resource)) {
            throw new \UnexpectedValueException('Resource type required');
        }
        
        $ret = new static();
        $ret->stream = $resource;
        $ret->rewind();
        return $ret;
    }
}
