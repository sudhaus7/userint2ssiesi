<?php


namespace SUDHAUS7\Sudhaus7Userint2ssiesi\Frontend\Renderer;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\RequiredArgumentMissingException;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class UserintPageRenderer
{
    
    /**
     * @var TypoScriptFrontendController
     */
    protected $tsfe;
    
    /**
     * @var ContentObjectRenderer
     */
    public $cObj;
    
    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $configCache = null;
    
    public function __construct()
    {
        $this->configCache = GeneralUtility::makeInstance(CacheManager::class)->getCache('sudhaus7userint2ssiesi_configcache');
        $this->hashService = GeneralUtility::makeInstance(HashService::class);
        $this->tsfe = $GLOBALS['TSFE'];
    }
    
    
    /**
     * @return string
     * @throws InvalidArgumentTypeException
     * @throws InvalidArgumentValueException
     * @throws NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException
     * @throws \TYPO3\CMS\Extbase\Security\Exception\InvalidHashException
     */
    public function render() : string
    {
        
       
        
        $args = GeneralUtility::_GET('tx_sudhaus7userin2ssiesi');
        
        if (!isset($args['identifier'])) {
            throw new NoSuchArgumentException('Required Argument identifier missing',1559478468);

        }
        if (empty($args['identifier'])) {
            throw new InvalidArgumentValueException('Argument identifier can not be empty',1559478521);
        }
        if ( !\is_string($args['identifier'])) {
           throw new InvalidArgumentTypeException('Argument identifier must be string',1559478565);
        }
        
        $cacheIdentifier = $this->hashService->validateAndStripHmac($args['identifier']);
    
        define('UserintPageRendererContext',true);
        
        
        $config = $this->configCache->get($cacheIdentifier);
        if (empty($config)) {
            // cache was probably cleared, and this is a stale link
            if (empty($this->tsfe->config['config']['additionalHeaders.']) || !\is_array($this->tsfe->config['config']['additionalHeaders.'])) {
                $this->tsfe->config['config']['additionalHeaders.']=[];
            }
            $this->tsfe->config['config']['additionalHeaders.'] += [
                'header' => 'Page not found',
                'httpResponseCode' => 404,
                'replace' => '1',
            ];
            $this->tsfe->set_no_cache('Cached Object not found');
            return '';
        }
    
        //TODO: implement cache_timeout setting for different types of contents
        //$this->tsfe->set_no_cache('Content is not cacheable');
        $this->tsfe->page['cache_timeout'] = 0;
      
        
        if (empty($config['cObj']) || empty($config['type'])) {
            return '';
        }
        
        $oldtype = $this->tsfe->type;
        $this->tsfe->type = 0;
        $oldchash = $this->tsfe->cHash;
        $this->tsfe->cHash='';
        
        
        //$this->tsfe->setPageArguments($pageArguments)
        
        $this->cObj = unserialize($config['cObj']);
        if (!$this->cObj instanceof ContentObjectRenderer) {
            return '';
        }
        
        $content = '';
        
        switch ($config['type']) {
            case 'COA':
                $content = $this->cObj->cObjGetSingle('COA', $config['conf']);
                break;
            case 'FUNC':
                $content = $this->cObj->cObjGetSingle('USER', $config['conf']);
                break;
            case 'POSTUSERFUNC':
                $content = $this->cObj->callUserFunction($config['postUserFunc'], $config['conf'], $config['content']);
                break;
        }
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->setTemplateFile('EXT:sudhaus7_userint2ssiesi/Resources/Private/Templates/UserintPageTemplate.html');
        $page = $pageRenderer->render(PageRenderer::PART_HEADER);
        $page .= $content;

        $pageRenderer->setTemplateFile('EXT:sudhaus7_userint2ssiesi/Resources/Private/Templates/UserintPageTemplate.html');
        $page .= $pageRenderer->render(PageRenderer::PART_FOOTER);
        return $page;
    }
}
