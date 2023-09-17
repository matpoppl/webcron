<?php

namespace matpoppl\Mailer;

use matpoppl\Mailer\Transport\TransportInterface;

class Mailer
{
    /** @var TransportInterface */
    private $transport;
    
    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }
    
    public function send(Message $msg)
    {
        return $this->transport->send($msg);
    }
}
