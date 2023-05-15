<?php

declare(strict_types=1);

return [
    'name'              => 'Server Name',
    'provider'          => 'Provider',
    'ip'                => 'IP',
    'ip_address'        => 'IP Address',
    'core'              => 'Core',
    'core_ver'          => 'Core Ver.',
    'core_version'      => 'Core Version',
    'core_with_version' => 'Core v:0',
    'type'              => 'Type',
    'disk'              => 'Disk',
    'ram'               => 'RAM',
    'cpu'               => 'CPU',
    'ping'              => 'Ping',
    'height'            => 'Height',
    'process'           => 'Process',
    'processes'         => 'Processes',
    'usage'             => 'Usage',
    'network'           => 'Network',
    'not_available'     => 'N/A',
    'manager_version'   => 'Manager Version',

    'actions' => [
        'restart'             => 'Restart',
        'start'               => 'Start',
        'stop'                => 'Stop',
        'update'              => 'Update',
        'delete'              => 'Delete',
        'update_core_version' => 'Update to v:version available',
        'manager'             => 'Manager',
    ],

    'providers' => [
        'aws'          => 'AWS',
        'azure'        => 'Azure',
        'digitalocean' => 'Digital Ocean',
        'google'       => 'Google',
        'hetzner'      => 'Hetzner',
        'linode'       => 'Linode',
        'netcup'       => 'Netcup',
        'ovh'          => 'OVH',
        'vultr'        => 'Vultr',
        'custom'       => 'Custom',
        'other'        => 'Other',
    ],

    'status' => [
        'online'                 => 'Online',
        'offline'                => 'Offline',
        'errored'                => 'Errored',
        'undefined'              => 'Undefined',
        'stopped'                => 'Stopped',
        'stopping'               => 'Stopping',
        'waiting_restart'        => 'Waiting Restart',
        'launching'              => 'Launching',
        'warning_status'         => 'Warning Status',
        'one_launch_status'      => 'One Launch Status',
        'deleted'                => 'Deleted',
        'updating_core'          => 'The server is being updated to Core v:version',
        'server_height_mismatch' => 'The server height is behind, please check your logs for any issues.',
        'unable_to_fetch_height' => 'Nodem was unable to fetch the latest height, please check your server.',
    ],

    'types' => [
        'all'     => 'All',
        'core'    => 'Core',
        'forger'  => 'Forger',
        'relay'   => 'Relay',
        'manager' => 'Core Manager',
    ],

    'tooltips' => [
        'process_not_running'    => 'The Manager process is currently not running. Please start the process to continue Node Management via Nodem.',
        'connection_failed'      => 'Connectivity to :server_name cannot be established. Please check that the server is online.',
        'no_permission'          => 'You do not have permission to :action.',
        'server_height_mismatch' => 'The server height is behind, please check your logs for any issues.',
    ],
];
