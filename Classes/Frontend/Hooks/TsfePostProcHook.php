<?php
namespace SUDHAUS7\Sudhaus7Userint2ssiesi\Frontend\Hooks;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class TsfePostProcHook
{
    const SSITEMPLATE = '<!--#include virtual="%s"-->';
    const ESITEMPLATE = '<!--esi <esi:include src="%s"/>-->';
    
    
    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $configCache = null;
    
    /**
     * @var TypoScriptFrontendController
     */
    protected $tsfe;
    
    
    /**
     * @var HashService
     */
    protected $hashService;
    
    public function __construct()
    {
        $this->configCache = GeneralUtility::makeInstance(CacheManager::class)->getCache('sudhaus7userint2ssiesi_configcache');
        $this->hashService = GeneralUtility::makeInstance(HashService::class);
    }
    
    public function handle(&$params) : void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return;
        }
    
        // this is a pointer
        $this->tsfe = $params['pObj'];
    
        if (!empty($this->tsfe->config['INTincScript'])) {
            
            $aSearch = [];
            $aReplace = [];
            
            $cacheTag = 'page_'.$this->tsfe->newHash;
            
            /* If we are here it means the pageCache has been cleared or it is a new page */
            $this->configCache->flushByTag($cacheTag);
            
            foreach ($this->tsfe->config['INTincScript'] as $identifier=>$config) {
                $cacheKey = hash('sha256', \serialize($config));
                
                $this->configCache->set($cacheKey, $config, [$cacheTag,'pageId_'.$this->tsfe->id]);
                
                if (strpos($this->tsfe->content, '<!--'.$identifier.'-->')!==false) {
                    $aSearch[]='<!--'.$identifier.'-->';
                    $link = $this->tsfe->cObj->typoLink_URL(
                        [
                            'parameter' => implode(',', [
                                $this->tsfe->id,
                                1559471597,
                            ]),
                            'forceAbsoluteUrl' => 1,
                            'addQueryString.' => [
                                'method' => $this->tsfe->cHash ? 'GET' : ''
                            ],
                            'additionalParams' => '&tx_sudhaus7userin2ssiesi[identifier]=' . $this->hashService->appendHmac($cacheKey),
                            'useCacheHash' => 1,
                        ]
                    );
                    // TODO: make configurable
                    $aReplace[]=sprintf(TsfePostProcHook::SSITEMPLATE, $link);
                    unset($this->tsfe->config['INTincScript'][$identifier]);
                }
            }
            $this->tsfe->content = str_replace($aSearch, $aReplace, $this->tsfe->content);
        }
        if (empty($this->tsfe->config['INTincScript']) && \is_array($this->tsfe->config['INTincScript'])) {
            
            // lets do what needs to be done in this context
            $this->tsfe->INTincScript();
            // and remove the array
            unset($this->tsfe->config['INTincScript']);
        }
    }
    
}
