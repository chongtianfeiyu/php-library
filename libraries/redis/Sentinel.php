<?php
/**
 * 连接Redis Sentinel
 *
 * @category Redis
 * @package  Sentinel_Client
 * @author   Lancer He <lancer.he@gmail.com>
 * @version  1.0 
 */

class Redis_Sentinel {

    /**
     * 通过TCP连接上Sentinel
     */
    protected static $_connected = false;

    protected $_master_name = '';

    protected $_master = [];

    protected $_slaves = [];

    /**
     * Redis_Sentinel_Client
     */
    protected $_Clients = [];

    /**
     * 初始化需要连接的Master
     */
    public function __construct($master_name) {
        $this->_master_name = $master_name;
    }

    protected function _connect() {
        if ( self::$_connected ) {
            return;
        }
        $this->_connectEachIfNotConnected();
        self::$_connected = true;
    }

    protected function _connectEachIfNotConnected() {
        foreach ($this->_Clients as $Client) {
            try {
                $this->_masters = $Client->masters();
                $this->_slaves  = $Client->slaves($this->_master_name);
                return;
            } catch (Redis_Sentinel_ConnectionTcpExecption $e) {
                $this->_writeOutputException($Client, $e);
            }
        }
        throw new Redis_Sentinel_ConnectionFailureExecption();
    }

    protected function _writeOutputException(Redis_Sentinel_Client $Client, Exception $e) {
        $output = "[". date('Y-m-d H:i:s'). "] " . $Client->getHost() . ":" . $Client->getPort() . " " . $e->getMessage() . PHP_EOL;
        file_put_contents("/tmp/redis_sentinel_exception.log", $output, FILE_APPEND);
    }

    public function add(Redis_Sentinel_Client $Client) {
        $this->_Clients[] = $Client;
    }

    public function getMaster() {
        $this->_connect();
        $masters = [];
        foreach ($this->_masters as $master) {
            $masters[$master['name']] = $master;
        }
        return $masters[$this->_master_name];
    }

    public function getSlaves() {
        $this->_connect();
        $slaves = [];
        foreach($this->_slaves as $slave) 
            if($slave['flags'] == 'slave') $slaves[] = $slave;
        return $slaves;
    }

    public function getSlave() {
        $slaves = $this->getSlaves();
        $idx = rand(0, count($slaves) - 1);
        return $slaves[$idx];
    }
}