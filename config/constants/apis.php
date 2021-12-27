<?php

    return [


        /* ERROR: GENERAL */
        'ERRCODE_NO_ERROR'                    => '0',
        'ERRCODE_INTERNAL_ERROR'              => '-1',
        'ERRCODE_ACCESS_TOKEN_NOT_FOUND'      => '-2',
        'ERRCODE_OAUTH2_ERROR_INVALID_CLIENT' => '-3',
        'ERRCODE_ACCESS_TOKEN_INVALID'        => '-4',

        'ERRCODE_INFOS_MANDATORY_PARAM_NOT_FOUND' => '-12',
        'ERRCODE_INFOS_PARAM_INVALID'             => '-13',
        'ERRCODE_INFOS_ROUTE_MODEL_NOT_FOUND'     => '-14',

        'ERRCODE_TELEGRAM_NOT_LOGGED'        => '-20',
        'ERRCODE_TELEGRAM_CHANNEL_NOT_FOUND' => '-21',


        'REST_ERRORS' => [

            /* ERROR: GENERAL */
            '0'  => ['msg' => ''],
            '-1' => ['msg' => 'Internal error.'],
            '-2' => ['msg' => 'Login required'],
            '-3' => ['msg' => 'Invalid client credentials'],
            '-4' => ['msg' => 'Invalid token'],


            '-12' => ['msg' => 'Mandatory field @FIELD not found'],
            '-13' => ['msg' => 'The value is invalid. check it value and format'],
            '-14' => ['msg' => 'The given id not match with any element'],

            '-20' => ['msg' => 'Don\'t have a Telegram session started'],
            '-21' => ['msg' => 'The given channel not found'],
        ],


    ];
