<?php
return [
    'servers' =>
        [
            [        // A memcached server hostname or IP address.
                'host' => '172.31.20.26',
                // Whether or not to use a persistent connection.
                'persistent' => true,
                // The memcached server port.
                'port' => 11211,
                // How often a failed server will be retried (in seconds).
                'retryInterval' => 15,
                // If the server should be flagged as online upon a failure.
                'status' => true,
                // The value in seconds which will be used for connecting to the server.
                'timeout' => 15,
                // Probability of using this server among all servers.
                'weight' => 1,
            ]
        ]
];