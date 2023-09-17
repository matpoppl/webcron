<?php

namespace matpoppl\Mailer\Transport;

use matpoppl\Mailer\Message;
use matpoppl\Logger\LoggerInterface;

use const STREAM_CRYPTO_METHOD_ANY_CLIENT, STREAM_CRYPTO_METHOD_TLS_CLIENT;

class SmtpTransport implements TransportInterface
{
    private $logger = null;
    /** @var resource */
    private $socket = null;
    private $connected = false;
    
    private $hostname = 'localhost';
    private $port = 25;
    private $authMode = null;
    private $encryption = null;
    private $socketCryptoType = null;
    private $username;
    private $password;
    
    private $sslContextOptions = [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true,
        //'cafile' => 'file.crt',
        //'cafile' => 'c:/Users/mateusz/OneDrive/OneDrive/ca/ca/cacert.pem',
        //'capath' => '/configs/certs', // filename:certhash.0
    ];
    
    public function __destruct()
    {
        $this->close();
    }
    
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }
    
    public function setPort($port)
    {
        $this->port = (int) $port;
    }
    
    public function setAuthMode($authMode)
    {
        switch ($authMode) {
            case 'NONE':
                $this->authMode = null;
                break;
            case 'PLAIN':
            case 'LOGIN':
            case 'CRAM-MD5':
                $this->authMode = $authMode;
            break;
            default:
                throw new \InvalidArgumentException('Unsupported authmode');
        }
    }
    
    public function setEncryption($encryption)
    {
        switch ($encryption) {
            case 'NONE':
                $this->encryption = null;
                break;
            case 'SSL':
            case 'TLS':
            case 'STARTTLS':
                $this->encryption = $encryption;
            break;
            default:
                throw new \InvalidArgumentException('Unsupported encryption');
        }
        
        switch ($encryption) {
            case 'NONE':
                $this->socketCryptoType = null;
                break;
            case 'SSL':
                $this->socketCryptoType = STREAM_CRYPTO_METHOD_ANY_CLIENT;
                break;
            case 'TLS':
            case 'STARTTLS':
                $this->socketCryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                break;
        }
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    public function close()
    {
        if ($this->socket) {
            $this->write(250, "QUIT");
            
            fclose($this->socket);
        }
        
        $this->socket = null;
        
        return $this;
    }
    
    public function connect()
    {
        if ($this->connected) {
            return $this;
        }
        
        $this->connected = true;
        
        switch ($this->encryption) {
            case 'SSL':
                $scheme = 'ssl'; // ssl
                break;
            case 'TLS':
                $scheme = 'tls'; // ssl
                break;
            default:
                $scheme = 'tcp'; // ssl
                break;
        }
        
        $errno = null;
        $errmsg = null;
        $timeout = 1.0;
        
        $uri = "{$scheme}://{$this->hostname}:{$this->port}";
        
        $ctx = stream_context_create([
            'ssl' => $this->sslContextOptions,
        ]);
        
        $socket = stream_socket_client(
            $uri,
            $errno,
            $errmsg,
            $timeout,
            STREAM_CLIENT_CONNECT ,
            $ctx
        );
        
        if (! $socket) {
            throw TransportException::fromLastError();
        }
        
        $this->socket = $socket;
        
        return $this;
    }
    
    public function open()
    {
        if ($this->connected) {
            return $this;
        }
        
        $this->connect();
        
        $this->read();
        
        $hostname = gethostname();
        
        switch ($this->encryption) {
            case 'TLS':
            case 'SSL':
                $this->writeStrict(250, "EHLO {$hostname}");
                break;
            case 'STARTTLS':
                $this->writeStrict(250, "EHLO {$hostname}");
                $this->cryptoSTARTTLS();
                break;
            default:
                $this->writeStrict(250, "HELO {$hostname}");
        }
        
        switch ($this->authMode) {
            case 'PLAIN':
                $this->writeStrict(334, 'AUTH PLAIN');
                $this->writeStrict(235, base64_encode("\0{$this->username}\0{$this->password}"));
                break;
            case 'LOGIN':
                $this->writeStrict(334, 'AUTH LOGIN');
                $this->writeStrict(334, base64_encode($this->username));
                $this->writeStrict(235, base64_encode($this->password));
                break;
            case 'CRAM-MD5':
                $nonce = base64_decode(substr($this->writeStrict(334, 'AUTH CRAM-MD5'), 4));
                $hash = hash_hmac('md5', $nonce, $this->password);
                $this->writeStrict(235, base64_encode("{$this->username} {$hash}"));
                break;
            default:
                throw new TransportException('Unsupported authmode');
        }
    }
    
    public function cryptoSTARTTLS()
    {
        $this->write(220, 'STARTTLS');
        
        $ok = stream_socket_enable_crypto(
            $this->socket,
            true,
            $this->socketCryptoType
        );
        
        if (! $ok) {
            throw TransportException::fromLastError($this->socket);
        }
    }
    
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function debug($msg)
    {
        if ($this->logger) {
            assert($this->logger->debug($msg));
        }
        
        return $this;
    }
    
    public function read($length = null, $mode = null)
    {
        $data = fread($this->socket, $length > 0 ? $length : 4096);
        
        if (false === $data) {
            throw TransportException::fromLastError();
        }
        
        $this->debug($data);
        
        return $data;
    }
    
    public function write($data)
    {
        $this->debug($data);
        return fwrite($this->socket, $data . "\r\n");
    }
    
    public function writeStrict($expectedCode, $data)
    {
        $this->write($data);
        
        $response = $this->read();
        $code = (int) $response;
        
        if ($expectedCode !== $code) {
            throw new TransportException("Expected code mismatch `{$expectedCode}!={$code}`");
        }
        
        return $response;
    }
    
    public function send(Message $msg)
    {
        if (! $this->connected) {
            $this->open();
        }
        
        $from = current($msg->getHeaders()->getEmail('From'));
        $to = current($msg->getHeaders()->getEmail('To'));
        
        /*
        if ($from !== filter_var($from, FILTER_VALIDATE_EMAIL)) {
            throw new \UnexpectedValueException("Invalid email From: <{$from}>");
        }
        
        if ($to !== filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new \UnexpectedValueException("Invalid email To: <{$to}>");
        }
        */
        
        $this->writeStrict(250, "MAIL FROM:<{$from}>");
        $this->writeStrict(250, "RCPT TO:<{$to}>");
        $this->writeStrict(354, "DATA");
        $this->writeStrict(250, $msg->__toString() . "\r\n.");
        /*
        */
/*
          S: <wait for connection on TCP port 25>
          C: <open connection to server>
          S: 220 dbc.mtview.ca.us SMTP service ready
          C: EHLO ymir.claremont.edu
          S: 250-dbc.mtview.ca.us says hello
          S: 250 8BITMIME
          C: MAIL FROM:<ned@ymir.claremont.edu> BODY=8BITMIME SIZE=500000
          S: 250 <ned@ymir.claremont.edu>... Sender and 8BITMIME ok
          C: RCPT TO:<mrose@dbc.mtview.ca.us>
          S: 250 <mrose@dbc.mtview.ca.us>... Recipient ok
          C: DATA
          S: 354 Send 8BITMIME message, ending in CRLF.CRLF.
           ...
          C: .
          S: 250 OK
          C: QUIT
          S: 250 Goodbye
*/
    }
}
