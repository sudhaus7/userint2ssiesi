<?php


namespace SUDHAUS7\Sudhaus7Userint2ssiesi\Frontend\Hooks;


class PageLinkHandler extends \TYPO3\CMS\Core\LinkHandling\PageLinkHandler
{
    public function resolveHandlerData(array $data): array
    {
        
        if (defined('UserintPageRendererContext') && UserintPageRendererContext) {
            unset($data['type']);
            unset($data['pagetype']);
            unset($data['tx_sudhaus7userin2ssiesi']);
        }
        return parent::resolveHandlerData($data);
    }
    
}
