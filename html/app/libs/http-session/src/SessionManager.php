<?php

namespace matpoppl\HttpSession;

use Psr\Container\ContainerInterface;

class SessionManager implements ContainerInterface
{
    /** @var bool */
    private static $started = false;
    
    /** @var array */
    private $options = [];
    
    /** @var array */
    private $config = [
        'use_cookies' => true,
        'use_only_cookies' => true,
        'cookie_path' => '/',
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'cookie_secure' => null,
        //'cookie_domain' => '',
        
        'cookie_lifetime' => 0,
        'cache_limiter' => 'nocache',
        'cache_expire' =>  0,
        
        'referer_check' => true,
        'use_strict_mode' => true,
        'use_trans_sid' => false,
        
        'gc_probability' => 1,
        'gc_divisor' => 1000,
        'gc_maxlifetime' => 86400 * 5,
        
        'sid_length' => 32,
        'sid_bits_per_character' => 5,
        
        'lazy_write' => true,
    ];
    
    /** @var NamespaceContainer */
    private $namespaces;
    
    public function __construct(array $options = null, array $config = null)
    {
        $this->namespaces = new NamespaceContainer();
        
        if (null !== $options) {
            $this->setOptions($options);
        }
        
        if (null !== $config) {
            $this->setConfig($config);
        }
    }
    
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }
    
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }
    
    public function setCookieLifetime($lifetime)
    {
        $this->config['cookie_lifetime'] = (int) $lifetime;
        return $this;
    }
    
    public function start(array $config = null)
    {
        if (self::$started) {
            return $this;
        }
        
        self::$started = true;
        
        /*
         session_cache_expire(0);
         session_cache_limiter('nocache');
         session_save_path('var/session');
         session_set_cookie_params(0, '/', null, null, true);
         */
        
        if (null === $this->config['cookie_secure']) {
            //$https = $_SERVER['REQUEST_SCHEME'] ?? 'http';
            $https = $_SERVER['HTTPS'] ?? 'off';
            $this->config['cookie_secure'] = ('on' === $https);
        }
        
        if (null === $config) {
            $ok = session_start($this->config);
        } else {
            $ok = session_start($config + $this->config);
        }
        
        if (! $ok) {
            throw new \UnexpectedValueException('Session start error');
        }
        
        // auto-save on shutdown
        //register_shutdown_function([$this, 'close']);
        
        return $this;
    }
    
    public function regenerateID()
    {
        session_regenerate_id(true);
    }
    
    public function getID()
    {
        return session_id();
    }
    
    public function getName()
    {
        return session_name();
    }
    
    public function getStatus()
    {
        // PHP_SESSION_*
        return session_status();
    }
    
    public function has(string $id): bool
    {
        return $this->namespaces->has($id);
    }
    
    
    public function get(string $id)
    {
        if (! self::$started) {
            $this->start();
        }
        
        return $this->namespaces->get($id);
    }
    
    public function close()
    {
        if (self::$started) {
            $this->namespaces->write();
            session_write_close();
        }
        
        self::$started = false;
        
        return $this;
    }
    
    public function abort()
    {
        if (self::$started) {
            session_abort();
        }
        
        self::$started = false;
        
        return $this;
    }
    
    public function reset()
    {
        session_reset();
        $this->namespaces->reset();
    }
    
    public function destroy()
    {
        session_destroy();
        $this->namespaces->destroy();
    }
}
