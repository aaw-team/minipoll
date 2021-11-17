<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "minipoll".
 *
 * Auto generated 13-03-2017 13:18
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Minipoll',
    'description' => 'A simple, lightweight poll plugin',
    'category' => 'plugin',
    'author' => 'Agentur am Wasser | Maeder & Partner AG',
    'author_email' => 'development@agenturamwasser.ch',
    'state' => 'alpha',
    'clearCacheOnLoad' => false,
    'version' => '1.0.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '7.2',
            'typo3' => '10.4.20-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [
            //'captcha' => '2.0.2',
            'sr_freecap' => '2.6.0'
        ],
    ],
];
