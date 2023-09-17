<?php

namespace matpoppl\HttpSession\Writer;

use matpoppl\DBAL\DBALInterface;

class DbalWriter implements \SessionHandlerInterface
{
    private $options = [
        'lifetime' => 86400 * 5,
    ];
    
    /** @var string */
    private $sidPrefix;

    /** @var DBALInterface */
    private $dbal;
    
    public function __construct(DBALInterface $dbal, array $options = null)
    {
        $this->dbal = $dbal;
        if (null !== $options) {
            $this->options += $options;
        }
    }
    
    public function register()
    {
        $ok = session_set_save_handler($this, true);
        
        if (! $ok) {
            throw new \UnexpectedValueException('Session save handler registration failed');
        }
        
        return $this;
    }
    
    public function open(string $save_path, string $session_name)
    {
        $this->sidPrefix = $save_path . '.' . $session_name . '.';
        return $this;
    }
    
    public function read(string $session_id)
    {
        $sid = $this->sidPrefix . $session_id;
        $sql = 'SELECT `expires`, `data` FROM `{session}` WHERE `sid`=?';
        $expired = time() - $this->options['lifetime'];
        foreach ($this->dbal->query($sql, [$sid]) as $row) {
            return $expired > $row['expires'] ? $row['data'] : '';
        }
        return '';
    }

    public function write(string $session_id, string $session_data)
    {
        $sid = $this->sidPrefix . $session_id;
        $expires = time() + $this->options['lifetime'];
        
        if (true) {
            $sql = 'INSERT INTO `{session}` (`sid`,`expires`,`data`) VALUES (?, ?, ?)';
            $this->dbal->query($sql, [$sid, $expires, $session_data]);
        } else {
            $sql = 'UPDATE `{session}` SET `expires`=?, `data`=? WHERE `sid`=?';
            $this->dbal->query($sql, [$expires, $session_data, $sid]);
        }
        
        return $this;
    }
    
    public function gc(int $maxlifetime)
    {
        $sid = $this->sidPrefix;
        $sql = 'DELETE FROM `{session}` WHERE `sid` LIKE ? AND `expires`<?';
        $this->dbal->query($sql, [$sid . '%', time() - $maxlifetime]);
        return true;
    }
    
    public function destroy(string $session_id)
    {
        $sid = $this->sidPrefix . $session_id;
        $sql = 'DELETE FROM `{session}` WHERE `sid`=?';
        $this->dbal->query($sql, [$sid]);
        return $this;
    }
    
    public function close()
    {
        $this->sidPrefix = null;
        return $this;
    }
}
