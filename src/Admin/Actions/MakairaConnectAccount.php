<?php

declare(strict_types=1);

namespace GXModules\MakairaIO\MakairaConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleStatusService;
use GXModules\MakairaIO\MakairaConnect\App\ChangesService;

/**s
 * Class MakairaConnectAccount
 *
 * @package GXModules\MakairaIO\MakairaConnect\Admin\Actions
 */
class MakairaConnectAccount extends AdminModuleAction
{
    private $templatePath = __DIR__ . '/../ui/template/account.html';
    private $templatePathInSetup = __DIR__ . '/../ui/template/in-setup.html';
    private $title = 'title';

    public function __construct(
        protected ModuleStatusService $moduleStatusService,
        protected ModuleConfigService $moduleConfigService,
        protected ChangesService $changesService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        if ($this->moduleStatusService->isInSetup()) {
            $template = $this->render(
                $this->translate($this->title, 'makaira_connect_general'),
                $this->templatePathInSetup,
            );
            return $response->write($template);
        }

        if ($request->isPost()) {
            return $this->handlePost($request, $response);
        }

        return $this->handleGet($request, $response);
    }

    private function getData(): array
    {
        return [
            'gambio_connect_form_url' => $this->url->admin(),
            'status' => $this->moduleConfigService->getStatus(),
            'makairaLink' => rtrim($this->moduleConfigService->getMakairaUrl(), "/") . '/admin/' .  $this->moduleConfigService->getMakairaInstance(),
            'makairaUrl' => $this->moduleConfigService->getMakairaUrl(),
            'makairaInstance'  => $this->moduleConfigService->getMakairaInstance(),
            'cron_status_1' => $this->translate('cron_status_1', 'makaira_connect_general'),
            'cron_status_0' => $this->translate('cron_status_0', 'makaira_connect_general'),
            'cronStatus' => $this->moduleConfigService->getCronjobStatus() ? $this->translate('cron_status_1', 'makaira_connect_general') : $this->translate('cron_status_0', 'makaira_connect_general'),
            'queueLength' => $this->changesService->getQueueLength(),
            'recoCrossSelling' =>  $this->moduleConfigService->getRecoCrossSelling(),
            'recoReversCrossSelling' => $this->moduleConfigService->getRecoReverseCrossSelling(),
        ];
    }
    private function handleGet(Request $request, Response $response): Response
    {

        $template = $this->render(
            $this->translate($this->title, 'makaira_connect_general'),
            $this->templatePath,
            $this->getData()
        );

        return $response->write($template);
    }

    private function handlePost(Request $request, Response $response): Response
    {
        $requestData = $request->getParsedBody();

        $recoCrossSelling = htmlspecialchars($requestData['recoCrossSelling']);
        $this->moduleConfigService->setRecoCrossSelling($recoCrossSelling);

        $recoReversCrossSelling = htmlspecialchars($requestData['recoReversCrossSelling']);
        $this->moduleConfigService->setRecoReverseCrossSelling($recoReversCrossSelling);


        $dataValidation = [
            'recoCrossSelling' =>  $recoCrossSelling,
            'recoReversCrossSelling' => $recoReversCrossSelling,
        ];

        $template = $this->render(
            $this->translate($this->title, 'makaira_connect_general'),
            $this->templatePath,
            array_merge($this->getData(), $dataValidation)
        );

        return $response->write($template);
    }
}
