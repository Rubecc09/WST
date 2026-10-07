<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * Directory containing database migrations and seeds.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Default database connection group.
     */
    public string $defaultGroup = 'default';

    /**
     * Main application database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'electric_company',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => false,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => [
            'ssl_key'    => null,
            'ssl_cert'   => null,
            'ssl_ca'     => null,
            'ssl_capath' => null,
            'ssl_cipher' => null,
            'ssl_verify' => false,
        ],
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * Database connection used by automated tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->default['hostname'] = 'mysql-a7469e1-wer-electric.j.aivencloud.com';

        $this->default['port'] = (int) (
            getenv('AIVEN_DB_PORT')
            ?: $this->default['port']
        );

        $this->default['username'] = getenv('AIVEN_DB_USER')
            ?: $this->default['username'];

        $this->default['password'] = getenv('AIVEN_DB_PASSWORD')
            ?: $this->default['password'];

        $this->default['database'] = getenv('AIVEN_DB_NAME')
            ?: $this->default['database'];

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
