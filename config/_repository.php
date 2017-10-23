<?php
return [
    "framework" =>
        [
            "release" => [
                "type" => "githubRelease",
                "source" => "hostingondemand/hodphp"
            ],

            "dev" => [
                "type" => "git",
                "source" => "git@github.com:hostingondemand/hodphp.git"
            ]
        ],
    "modules" => [
        "hoduser" => [
            "release" => [
                "name" => "hoduser",
                "type" => "githubRelease",
                "source" => "hostingondemand/hoduser"
            ],
            "dev" => [
                "name" => "hoduser",
                "type" => "git",
                "source" => "git@github.com:hostingondemand/hoduser.git"
            ],
        ],
        "hodclient" => [
            "release" => [
                "name" => "hodclient",
                "type" => "githubRelease",
                "source" => "hostingondemand/hodclient"
            ],
            "dev" => [
                "name" => "hodclient",
                "type" => "git",
                "source" => "git@github.com:hostingondemand/hodclient.git"
            ]
        ],
        "hoddbconfig" => [
            "release" => [
                "name" => "hoddbconfig",
                "type" => "githubRelease",
                "source" => "hostingondemand/hoddbconfig"
            ],
            "dev" => [
                "name" => "hoddbconfig",
                "type" => "git",
                "source" => "git@github.com:hostingondemand/hoddbconfig.git"
            ]
        ]
    ]
];

