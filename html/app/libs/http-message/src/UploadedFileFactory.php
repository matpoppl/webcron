<?php

namespace matpoppl\HttpMessage;

use Psr\Http\Message\UploadedFileInterface;

class UploadedFileFactory
{
    /**
     * 
     * @param array $files
     * @throws \OverflowException
     * @return UploadedFileInterface[]
     */
    public static function createFromArray(array $files)
    {
        $ret = [];
        
        if (array_key_exists('error', $files) && array_key_exists('tmp_name', $files) && array_key_exists('size', $files)) {
            if (is_array($files['tmp_name'])) {
                
                if (array_key_exists(0, $files['tmp_name'])) {
                    // expecting $_FILES[key][tmp_name][0] = phpBA3F.tmp
                    foreach (array_keys($files['tmp_name']) as $i) {
                        $ret[$i] = new UploadedFile(
                            $files['name'][$i],
                            $files['type'][$i],
                            $files['tmp_name'][$i],
                            $files['error'][$i],
                            $files['size'][$i]
                            );
                    }
                    return $ret;
                }
                
                // expecting $_FILES[key][tmp_name][subkey] ???
                throw new \RuntimeException('Not implemented');
                foreach (array_keys($files['tmp_name']) as $key) {
                    /*
                     $ret[$key] = new UFile(
                     $files['name'][$key],
                     $files['type'][$key],
                     $files['tmp_name'][$key],
                     $files['error'][$key],
                     $files['size'][$key]
                     );
                     */
                }
                
                return $ret;
            }
            
            // expecting $_FILES[key][tmp_name] = phpBA3F.tmp
            $ret[] = new UploadedFile($files['name'], $files['type'], $files['tmp_name'], $files['error'], $files['size']);
            return $ret;
        }
        
        // expecting $_FILES[key][tmp_name][0] = phpBA3F.tmp
        foreach (array_keys($files) as $key) {
            $ret[$key] = self::createFromArray($files[$key]);
        }
        
        return $ret;
    }
}
