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

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Minipoll',
    'description' => 'A simple, lightweight poll plugin',
    'category' => 'plugin',
    'author' => 'Agentur am Wasser | Maeder & Partner AG',
    'author_email' => 'development@agenturamwasser.ch',
    'state' => 'alpha',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0-dev',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.0-8.999.999'
        ),
        'conflicts' => array(),
        'suggests' => array(
            'captcha' => '2.0.2',
            'sr_freecap' => ''
        )
    )
);
