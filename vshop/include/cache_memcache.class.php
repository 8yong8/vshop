<?php

class cache_memcache {

    private $_memcached;

    protected $_memcached_conf = array(

        'default_host' => '127.0.0.1',

        'default_port' => 11211,

        'default_weight' => 1,

    );

    public function __construct() {

        $this->_memcached = new Memcache($this->_memcached_conf);

        $this->_memcached->addserver('127.0.0.1', '11211');

    }

    // Save data into cache

    public function save($key, $data, $ttl = 3600) {

        if (get_class($this->_memcached) == 'Memcached') {

            return $this->_memcached->set($key, array($data, time(), $ttl), $ttl);

        } else if (get_class($this->_memcached) == 'Memcache') {

            return $this->_memcached->set($key, array($data, time(), $ttl), 0, $ttl);
        }

        return false;

    }

    // Fetch data from cache

    public function get($key) {

        $data = $this->_memcached->get($key);

        return (is_array($data)) ? $data[0] : false;

    }

 

    // Detele data from cache

    public function delete($key) {

        return $this->_memcached->delete($key);

    }

    //  // clean will marks all the items as expired, so occupied memory will be overwritten by new items.

    public function clean() {

        return $this->_memcached->flush();

    }

 

    public function ServerStatus() {

        $server_status = $this->_memcached->getstats();

        echo "<table border='1'>";

        echo "<tr><td>Memcache Server version:</td><td> " . $server_status["version"] . "</td></tr>";

        echo "<tr><td>Process id of this server process </td><td>" . $server_status["pid"] . "</td></tr>";

        echo "<tr><td>Number of seconds this server has been running </td><td>" . $server_status["uptime"] . "</td></tr>";

        echo "<tr><td>Accumulated user time for this process </td><td>" . $server_status["rusage_user"] . " seconds</td></tr>";

        echo "<tr><td>Accumulated system time for this process </td><td>" . $server_status["rusage_system"] . " seconds</td></tr>";

        echo "<tr><td>Total number of items stored by this server ever since it started </td><td>" . $server_status["total_items"] . "</td></tr>";

        echo "<tr><td>Number of open connections </td><td>" . $server_status["curr_connections"] . "</td></tr>";

        echo "<tr><td>Total number of connections opened since the server started running </td><td>" . $server_status["total_connections"] . "</td></tr>";

        echo "<tr><td>Number of connection structures allocated by the server </td><td>" . $server_status["connection_structures"] . "</td></tr>";

        echo "<tr><td>Cumulative number of retrieval requests </td><td>" . $server_status["cmd_get"] . "</td></tr>";

        echo "<tr><td> Cumulative number of storage requests </td><td>" . $server_status["cmd_set"] . "</td></tr>";

        $percCacheHit = ((real) $server_status["get_hits"] / (real) $server_status["cmd_get"] * 100);

        $percCacheHit = round($percCacheHit, 3);

        $percCacheMiss = 100 - $percCacheHit;

        echo "<tr><td>Number of keys that have been requested and found present </td><td>" . $server_status["get_hits"] . " ($percCacheHit%)</td></tr>";

        echo "<tr><td>Number of items that have been requested and not found </td><td>" . $server_status["get_misses"] . "($percCacheMiss%)</td></tr>";

        $MBRead = (real) $server_status["bytes_read"] / (1024 * 1024);

        echo "<tr><td>Total number of bytes read by this server from network </td><td>" . $MBRead . " Mega Bytes</td></tr>";

        $MBWrite = (real) $server_status["bytes_written"] / (1024 * 1024);

        echo "<tr><td>Total number of bytes sent by this server to network </td><td>" . $MBWrite . " Mega Bytes</td></tr>";

        $MBSize = (real) $server_status["limit_maxbytes"] / (1024 * 1024);

        echo "<tr><td>Number of bytes this server is allowed to use for storage.</td><td>" . $MBSize . " Mega Bytes</td></tr>";

        echo "<tr><td>Number of valid items removed from cache to free memory for new items.</td><td>" . $server_status["evictions"] . "</td></tr>";

        echo "</table>";

    }

}

?>