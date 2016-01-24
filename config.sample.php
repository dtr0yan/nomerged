<?php

return [
    'gitBin' => '', // path to git binary
    'workDir' => '', // path to working dir with git repository you want to work with
    'trackedBranch' => '', // branch against script will check not merged branches
    'users' => [ // array of allowed users emails and additional parameters
        'someUserName' => [
            'name' => '', // name used in mail
            'emails' => [ // emails that branches can be pushed by
                'someemail@example.com',
                'someemailnew@example.com'
            ],
            'sendTo' => 'someemai@example.com' // notification for this user will be sent to this email
        ]
    ],
    'app' => [
        'name' => 'Pre-work Email', // app name
        'email' => [
            'from' => 'someemail@example.com', // From field in all mails
            'smtpServer' => 'aspmx.l.google.com',
            'port' => 25,
            'user' => 'username',
            'password' => 'userpassword'
        ]
    ],
    'log' => [
        'path' => '',
        'level' => \Monolog\Logger::DEBUG
    ]
];