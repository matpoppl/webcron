<?php

namespace matpoppl\ImageCaptcha;

use matpoppl\HttpSession\SessionNamespaceInterface;

class PhraseSource implements PhraseSourceInterface
{
    private $charset = '1234567890QWERTYUIOPLKJHGFDSAZXCVBNM';
    private $length = 8;
    /** @var SessionNamespaceInterface */
    private $storage;
    
    public function __construct(SessionNamespaceInterface $storage, array $options = null)
    {
        $this->storage = $storage;
        
        if (null === $options) {
            return;
        }
        
        if (isset($options['charset'])) {
            $this->charset = $options['charset'];
        }
        
        if (isset($options['length'])) {
            $this->length = $options['length'];
        }
    }
    
    public function getAndTick() : string
    {
        $new = $this->generatePhrase();
        $ret = $this->storage->get(PhraseSourceInterface::class) ?: $new;
        $this->storage->set(PhraseSourceInterface::class, $new);
        return $ret;
    }
    
    public function tickAndGet() : string
    {
        $new = $this->generatePhrase();
        $this->storage->set(PhraseSourceInterface::class, $new);
        return $new;
    }
    
    public function generatePhrase()
    {
        $ret = '';
        
        for ($i = 0; $i < $this->length; $i++) {
            $tmp = str_shuffle($this->charset);
            $ret .= $tmp[0];
        }
        
        return $ret;
    }
}
