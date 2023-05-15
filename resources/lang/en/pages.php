<?php

declare(strict_types=1);

return [
    'user-settings' => [
        'security' => [
            'page_title'        => 'Security Settings',
            'two_factor_alert'  => 'In order to use Nodem you have to enable 2FA below.',
            'two_factor_prompt' => [
                'title'       => 'Two-Factor Authentication',
                'description' => 'In order to use Nodem you first have to set Two-Factor Authentication (2FA). When 2FA is enabled, you will be prompted for a secure, random token during authentication.',
                'submit'      => 'Ok, I Got it',
            ],
        ],
    ],

    'home' => [
        'title'      => 'Node Manager',
        'no_servers' => 'It appears there are no servers for Nodem to manage. Use the Import or Add Server buttons above to get started!',
    ],

    'sign-in' => [
        'page_title'       => 'Sign In',
        'page_description' => 'Sign in to Nodem for easy and secure blockchain node management.',
        'sign_up_link'     => 'Have an invitation code? <a href=":route" class="font-semibold underline link">Sign up</a>',
    ],

    'sign-up' => [
        'page_title'       => 'Sign Up',
        'page_description' => 'Easy and Secure Blockchain Node Management',
    ],

    'server' => [
        'server_logs'         => 'Server Logs',
        'empty_activity_logs' => 'There are currently no :0 log entries',
        'empty_process_logs'  => 'There are currently no logs to display for this process.',
        'empty_search'        => 'We could not find anything in the logs matching your search criteria, please try again!',
        'information'         => 'Information',
        'configuration'       => 'Configuration',
        'logs'                => [
            'core'           => 'Core',
            'relay'          => 'Relay',
            'forger'         => 'Forger',
            'activity'       => 'Activity',
            'performance'    => 'Performance',
            'download_logs'  => 'Download Logs',
            'search_logs'    => 'Search Logs',
            'message_action' => 'Message (Action)',
            'search'         => [
                'placeholder'        => 'Search for logs by type or content',
                'placeholder_mobile' => 'Search...',
            ],
            'error' => 'There was an unexpected problem trying to fetch the logs from the server. Refresh the page to try again.',
        ],
        'performance' => [
            'chart' => [
                'prefix'   => 'Time:',
                'periods'  => [
                    'day'       => 'Day',
                    'week'      => 'Week',
                    'month'     => 'Month',
                    'year'      => 'Year',
                    'all'       => 'All time',
                ],
            ],
        ],
    ],

    'export-modal' => [
        'button'      => 'Export',
        'title'       => 'Export Servers',
        'description' => 'Your servers and any related properties will be exported to a .json file. You can import the .json at a later time to restore this page.',
        'cancel'      => 'Cancel',
        'submit'      => 'Download .json',
    ],

    'edit-server-modal' => [
        'title'        => 'Edit Server',
        'server_error' => 'Failed to connect to the server. Please check the configuration below and try again.',
        'inputs'       => [
            'provider_placeholder'       => 'Select a provider from the list',
            'provider'                   => 'Server Provider',
            'process_type'               => 'Process Type',
            'process_type_placeholder'   => 'Select the process type of the node',
            'server_name'                => 'Server Name',
            'server_name_placeholder'    => 'Give your server a memorable name',
            'server_address'             => 'Host',
            'server_address_placeholder' => 'https://example.com or 127.0.0.1:4005',
            'account'                    => 'Account',
            'access_key'                 => 'Access Key',
            'username'                   => 'Username',
            'username_placeholder'       => 'John Doe',
            'username_tooltip'           => 'The credentials required for this field can be located within the app.json on your server',
            'password'                   => 'Password',
            'uses_bip38_encryption'      => 'Forger uses BIP38 encryption',
        ],
    ],

    'add-server-modal' => [
        'title'                     => 'Add Server',
        'server_error'              => 'Failed to connect to the server. Please check the configuration below and try again.',
        'server_credentials_error'  => 'These credentials do not match our records.',
        'process_type'              => [
            'separate_server_error' => 'Server process type is set to "separate", but Nodem detected a single "core" process. Please recheck your configuration.',
            'combined_server_error' => 'Server process type is set to "combined", but Nodem detected separate processes. Please recheck your configuration.',
        ],
        'inputs'                    => [
            'provider_placeholder'       => 'Select a provider from the list',
            'provider'                   => 'Server Provider',
            'process_type'               => 'Process Type',
            'process_type_placeholder'   => 'Select the process type of the node',
            'server_name'                => 'Server Name',
            'server_name_placeholder'    => 'Give your server a memorable name',
            'server_address'             => 'Host',
            'server_address_placeholder' => 'https://example.com or 127.0.0.1:4005',
            'account'                    => 'Account',
            'access_key'                 => 'Access Key',
            'username'                   => 'Username',
            'username_placeholder'       => 'John Doe',
            'username_tooltip'           => 'The credentials required for this field can be located within the app.json on your server',
            'password'                   => 'Password',
            'uses_bip38_encryption'      => 'Forger uses BIP38 encryption',
        ],
    ],

    'remove-server-modal' => [
        'title'       => 'Remove Server',
        'description' => '<span class="font-semibold">Are you sure you want to remove this server from Nodem?</span> This action cannot be undone. To confirm this action, enter the server name below.',
        'inputs'      => [
            'server_name'                     => 'Server Name',
            'server_name_confirm_placeholder' => 'Enter the server name to confirm removal',
        ],
    ],

    'bip38-password-modal' => [
        'title'       => 'Enter BIP38 Password',
        'description' => 'Input your BIP38 encryption password to start the Forger process',
    ],

    'download-logs-modal' => [
        'title'      => 'Download Logs',
        'subtitle'   => 'Select parameters for logs you wish to download.',
        'date-from'  => 'Date From',
        'date-to'    => 'Date To',
        'time-from'  => 'Time From',
        'time-to'    => 'Time To',
        'levels'     => 'Log Types',
        'select-all' => 'Select All',
        'messages'   => [
            'date-format'       => 'Date is not in the valid format (DD.mm.YYYY)',
            'time-format'       => 'Time is not in the valid format (HH:mm:ss)',
            'required'          => 'This field is required.',
            'no-levels'         => 'Please select at least one log type.',
            'start-date-future' => 'Start date cannot be in the future.',
            'end-date-future'   => 'End date cannot be before the start date.',
            'unexpected'        => 'Unexpected error occurred trying to generate a log archive for the server. Please try again later.',
        ],
    ],

    'filter-logs-modal' => [
        'title'      => 'Filter Logs',
        'subtitle'   => 'Select parameters for logs you wish to filter.',
        'date-from'  => 'Date From',
        'date-to'    => 'Date To',
        'time-from'  => 'Time From',
        'time-to'    => 'Time To',
        'levels'     => 'Log Types',
        'messages'   => [
            'date-format'       => 'Date is not in the valid format (DD.mm.YYYY)',
            'time-format'       => 'Time is not in the valid format (HH:mm:ss)',
            'required'          => 'This field is required.',
            'no-levels'         => 'Please select at least one log type.',
            'start-date-future' => 'Start date cannot be in the future.',
            'end-date-future'   => 'End date cannot be before the start date.',
            'unexpected'        => 'Unexpected error occurred trying to generate a log archive for the server. Please try again later.',
        ],
    ],

    'team' => [
        'title'               => 'Team Management',
        'pending-invitations' => [
            'title'          => 'Pending Invitations',
            'no_invitations' => "You do not have any pending invitations. To invite a team member, define a username and generate an invite code by clicking the <strong>'Invite'</strong> button.",
        ],
        'members' => [
            'title' => 'Team Members',
        ],
        'invitation-modal' => [
            'title'                => 'Generate Invitation Code',
            'description'          => 'Generate a new invitation code by inputting the username and selecting a role for a new team member.',
            'invitation_success'   => 'Invitation succesfully sent !',
            'user_already_member'  => 'User is already part of this project.',
            'user_already_invited' => 'An invitation was already sent for this user.',
        ],
        'edit-modal' => [
            'title'          => 'Edit Team Member',
            'description'    => 'Modify the role of the selected team member.',
            'update_success' => 'The Team Member has been updated.',
        ],
        'remove-team-member-modal' => [
            'title'          => 'Remove Team Member',
            'description'    => 'Are you sure you want to delete <strong>:user</strong>? Removing a team member is permanent. You will be required to send a new invitation for the user to re-join the team.',
            'remove_success' => 'The Team Member has been removed.',
        ],
        'invite_title'       => 'Generate Invitation Code',
        'invite_description' => 'Generate a new invitation code by inputting the username and selecting a role  for a new team member.',
        'already_member'     => 'The user is already a Team Member.',
        'already_invited'    => 'The user has already been invited.',

        'pending_title'       => 'Invitation Code',
        'pending_description' => 'Copy and share the invitation code and username with a new team member.',

        'pending_invitations' => [
            'title' => 'Pending Invitations',

            'delete_modal' => [
                'title'       => 'Delete Invitation Code',
                'description' => 'Deleting the invitation code makes it invalid. Generating a new invitation code will be required for the user to join the team.',
            ],
        ],
    ],

    'import-servers' => [
        'title'                     => 'Import Servers',
        'description'               => 'Select a .json file with your server\'s configuration information to start the import process. The file has been generated during the export process.',
        'upload_description'        => [
            'drag_and_drop' => 'Drag & Drop or',
            'browse'        => 'Browse Files',
        ],
        'supported_format'          => 'Supported format is :format',
        'ongoing-import'            => [
            'title'       => 'Importing Servers',
            'description' => "The file ':filename' is being imported. Please wait...",
        ],
        'import-error' => [
            'title'       => 'Import Error',
            'description' => "An error occured while importing the servers configuration from the provided file. Press <strong>'Back'</strong> or import a different file.",
        ],
        'manage-import' => [
            'title'       => 'Import Servers',
            'description' => 'Select the servers you wish to import to your Node Manager.',
            'messages'    => [
                'cannot_connect_to_server'      => 'Cannot connect to server',
                'duplicated_server'             => 'Duplicated server',
                'problem_when_trying_to_import' => 'There was a problem while trying to import or connect to some of the servers. Click \'Retry\' to try again.',
            ],
        ],
        'complete-import' => [
            'title'           => 'Import Complete',
            'description'     => 'The selected servers have been succesfully imported.',
            'success_message' => '{1} Successfully imported :amount server|[2,*] Successfully imported :amount servers',
        ],
    ],

    'terms-of-service' => [
        'title' => 'Terms of Service',
    ],
];
