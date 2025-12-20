<?php
// src/redis_client.php
// Lightweight Redis client abstraction with naive fallback
// Intentionally trusts internal network

class RedisClient {
    private $host;
    private $port;
    private $conn;   // redis extension connection
    private $sock;   // raw socket fallback

    public function __construct(string $host = '127.0.0.1', int $port = 6379) {
        $this->host = $host;
        $this->port = $port;

        if (extension_loaded('redis')) {
            $this->conn = new Redis();
            $this->conn->connect($this->host, $this->port);
        }
    }

    public function set(string $key, string $value) {
        if ($this->conn) {
            return $this->conn->set($key, $value);
        }

        return $this->sendRaw(
            sprintf(
                "*3\r\n$3\r\nSET\r\n$%d\r\n%s\r\n$%d\r\n%s\r\n",
                strlen($key), $key,
                strlen($value), $value
            )
        );
    }

    public function get(string $key) {
        if ($this->conn) {
            return $this->conn->get($key);
        }

        $resp = $this->sendRaw(
            sprintf(
                "*2\r\n$3\r\nGET\r\n$%d\r\n%s\r\n",
                strlen($key), $key
            )
        );

        if (preg_match('/\$(\d+)\r\n(.+)\r\n/s', $resp, $m)) {
            return $m[2];
        }

        return false;
    }

    private function sendRaw(string $payload) {
        $fp = fsockopen($this->host, $this->port, $errno, $errstr, 2);
        if (!$fp) {
            return false;
        }

        fwrite($fp, $payload);
        $resp = fread($fp, 8192);
        fclose($fp);

        return $resp;
    }
}    