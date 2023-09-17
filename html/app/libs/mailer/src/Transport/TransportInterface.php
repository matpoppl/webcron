<?php

namespace matpoppl\Mailer\Transport;

use matpoppl\Mailer\Message;

interface TransportInterface
{
    public function send(Message $msg);
}
