<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][1559465686] = \SUDHAUS7\Sudhaus7Userint2ssiesi\Frontend\Hooks\TsfePostProcHook::class.'->handle';


$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['sudhaus7userint2ssiesi_configcache']=[
                'backend'=>\TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class,
                'frontend'=>\TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
                'groups'=>['pages'],
                'options'=>[
                    'defaultLifetime'=>0,
                ]
           
];


$GLOBALS['TYPO3_CONF_VARS']['SYS']['linkHandler']['page'] = \SUDHAUS7\Sudhaus7Userint2ssiesi\Frontend\Hooks\PageLinkHandler::class;
