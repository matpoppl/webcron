<?php

namespace matpoppl\Mailer\Part;

use matpoppl\Mailer\Headers;
use matpoppl\Mailer\Random;

class StreamPart implements PartInterface
{
    /** @var Headers */
    private $headers;
    /** @var resource */
    private $handle;
    
    private function __construct($handle, $headers = null)
    {
        $this->headers = ($headers instanceof Headers) ? $headers : new Headers($headers);
        $this->handle = $handle;
    }
    
    /** @return \matpoppl\Mailer\Headers */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    public function close()
    {
        if ($this->handle) {
            fclose($this->handle);
        }
        $this->handle = null;
        return $this;
    }
    
    public function getContents()
    {
        return stream_get_contents($this->handle);
    }
    
    public function rewind()
    {
        return rewind($this->handle);
    }
    
    public function __toString()
    {
        $ret = $this->getContents();
        $this->rewind();
        return wordwrap($ret, 72, "\r\n", true);
    }
    
    public static function createFromPathname($pathname, $contentType = null)
    {
        if (! $contentType) {
            if (! function_exists('mime_content_type')) {
                throw new \RuntimeException('`mime_content_type` function required');
            }
            
            $contentType = mime_content_type($pathname);
        }
        
        $fp = fopen($pathname, 'r');
        
        if (! $fp) {
            throw new \UnexpectedValueException('File open error');
        }
        
        stream_filter_append($fp, 'convert.base64-encode', \STREAM_FILTER_READ);
        
        $contentId = Random::createId(8);
        
        return new static($fp, [
            'Content-Type' => $contentType . '; name="'.basename($pathname).'"',
            'Content-Disposition' => 'attachment; filename="'.basename($pathname).'"',
            'Content-Transfer-Encoding' => 'base64',
            'Content-ID' => "<{$contentId}>",
            'X-Attachment-Id' => $contentId,
        ]);
    }
    
    public static function createFromString($data, $contentType)
    {
        $fp = fopen('php://memory', 'w+');
        
        if (! $fp) {
            throw new \UnexpectedValueException('Inmemory stream open error');
        }
        
        if (fwrite($fp, $data) !== strlen($data)) {
            throw new \RuntimeException('Stream write error');
        }
        
        if (! rewind($fp)) {
            throw new \RuntimeException('Stream rewind error');
        }
        
        //stream_filter_append($fp, 'convert.quoted-printable-encode', \STREAM_FILTER_READ);
        
        return new static($fp, [
            'Content-Type' => $contentType,
            //'Content-Transfer-Encoding' => 'quoted-printable',
        ]);
    }
}
