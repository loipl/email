<?php

// Including the configuration file
include_once dirname(__FILE__) . '/../Config.php';

class Stats {
    protected $db_connection;

    protected $metric_index = array(
        1 => 'b.campaign_id',
        2 => 'b.creative_id',
        3 => 'b.sender',
        4 => 'b.channel',
        5 => "SUBSTRING_INDEX(b.email, '@', -1)"
    );

    protected $metric_index_2 = array(
        1 => 'a.campaign_id',
        2 => 'a.creative_id',
        3 => 'a.sender',
        4 => 'a.channel',
        5 => "SUBSTRING_INDEX(a.email, '@', -1)"
    );

    protected $log_level = 'debug';

    protected function connect_db() {
        $database = Config::$database;
        $this->db_connection = new mysqli($database['host'],
            $database['username'],
            $database['password'],
            $database['database']);

        if ($this->db_connection->connect_errno) {
            $msg = "Failed to connect to MySQL: (" . $this->db_connection->connect_errno . ") " . $this->db_connection->connect_error;
            throw new Exception($msg);
        }

        $this->log_message("Connected to " . $this->db_connection->host_info, "debug");
    }

    protected function run_db_query($sql) {
        $this->log_message($sql, "debug");

        $result = $this->db_connection->query($sql);
        if (!$result) {
            $msg = "Query failed: (" . $this->db_connection->errno . ")" . $this->db_connection->error;
            throw new Exception($msg);
        }

        return $result;
    }

    protected function rollback_trx() {
        $this->run_db_query('ROLLBACK');
        return false;
    }

    protected function log_message($msg, $msg_type) {
        if ($msg_type == $this->log_level) {
            $current_datetime = new DateTime('NOW');
            $datetime_format='Y-m-d H:i:s';

            echo "\n[" . $current_datetime->format($datetime_format) . "] " . $msg;
        }
    }

    public function send_email($to, $from, $subject, $message) {
        $message = wordwrap($message, 70, "\r\n");
        $headers = 'From: ' . $from;
        mail($to, $subject, $message, $headers);
    }

    public static function read_pid_from_file($filename) {
        return file_get_contents($filename);
    }

    // source: https://gist.github.com/JuliusBeckmann/3096556
    public static function is_pid_running($pid) {
        $lines_out = array();
        exec('ps '.(int)$pid, $lines_out);
        if(count($lines_out) >= 2) {
            // Process is running
            return true;
        }
        return false;
    }
}
