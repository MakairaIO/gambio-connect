<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class GambioConnectWelcome
 *
 * @package GXModules\Makaira\GambioConnect\App\Actions
 */
class GambioConnectWelcome extends AdminModuleAction
{
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $pageTitle    = 'Makaira Gambio FAQs';
        $templatePath = __DIR__ . '/../../ui/template/welcome.html';

        // $makairaUrl = $this->moduleConfig->makairaUrl();
        // $makairaInstance = $this->moduleConfig->makairaInstance();
        // $language = $this->languageService->getLanguageById($languageId);
        // $makairaRequest = new MakairaRequest($makairaUrl, $makairaInstance, $language->code());
        // $pageData = $makairaRequest->fetchPageData('/');
        // $components = $makairaRequest->getPageComponents($pageData);

        $data = [
            'overviewJs' => "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/overview.js",
            'heading' =>  '',
            // 'pageData' => $pageData,
            // 'components' => $components
        ];
        $template = $this->render($pageTitle, $templatePath, $data);

        return $response->write($template);
    }
}
