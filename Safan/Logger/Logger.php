<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\Logger;

class Logger
{
    /**
     * All requirement logs for framework
     *
     * @var array
     */
    private $logs = [];

    /**
     * @param $logFile
     * @param $message
     * @return bool
     */
    public function log(string $logFile, string $message)
    {
        if (!is_readable(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }

        $fh = fopen($logFile, 'a');

        if ($fh) {
            $date = date('Y-m-d H:i:s', time());
            $message = "\n+++++----------------------------------+++++\n$date\n$message";
            fwrite($fh, $message);
            fclose($fh);

            return true;
        }

        return false;
    }

    /**
     * Get Logs
     *
     * @param bool $key
     * @return array
     */
    public function getLogs($key = false): array
    {
        if ($key) {
            return isset($this->logs[$key]) ? $this->logs[$key] : false;
        }

        return $this->logs;
    }

    /**
     * Set Log
     *
     * @param string $logKey
     * @param array $logParams
     */
    public function setLog(string $logKey, array $logParams = [])
    {
        $this->logs[$logKey] = $logParams;
    }

    /**
     * check logs
     * @return boolean
     */
    public function checkLogs(): bool
    {
        if (empty($this->logs)) {
            return false;
        }

        return true;
    }

    /**
     * Print Log
     */
    public function printLogs()
    {
        if (!$this->checkLogs()) {
            $color = 'green';
            $message = 'All requirements successfully installed';
        } else {
            $color = 'red';
            $message = '';

            foreach ($this->logs as $value) {
                $message .= $value . '<br/>';
            }
        }

        echo "<div class='DUMP' style='border: 2px solid #000000;
                                                background-color: green;
                                                color:" . $color . "
                                                ;position: relative;
                                                z-index: 100000;
                                                margin:5px;
                                                padding: 5px;
                                                -webkit-border-radius:5px;
                                                -moz-border-radius:5px;
                                                -o-border-radius:5px;
                                                border-radius:5px;
                                                word-wrap:break-word'>" . $message . "</div>";

    }
}
