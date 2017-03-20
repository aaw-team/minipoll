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
            'captcha' => ''
        )
    ),
    'autoload' => array(
        'psr-4' => array(
            'AawTeam\\Minipoll\\' => 'Classes/'
        )
    )
);

// Add dependencies to autoloading if needed. This is the case when TYPO3 does
// not run in composer mode (in this case the autoload section from EM_CONF is
// ignored anyway)
if (version_compare(TYPO3_version, '8', '<')) {
    if (!class_exists('ParagonIE\\ConstantTime\\Encoding')) {
        $EM_CONF[$_EXTKEY]['autoload']['psr-4']['ParagonIE\\ConstantTime\\'] = 'code/vendor/paragonie/constant_time_encoding/src/';
    }
}
