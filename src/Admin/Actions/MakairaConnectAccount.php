<?php

declare(strict_types=1);

namespace GXModules\Makaira\MakairaConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Makaira\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\MakairaConnect\Admin\Services\ModuleStatusService;
use GXModules\Makaira\MakairaConnect\App\ChangesService;
use Respect\Validation\Validator as v;

/**s
 * Class MakairaConnectAccount
 *
 * @package GXModules\Makaira\MakairaConnect\Admin\Actions
 */
class MakairaConnectAccount extends AdminModuleAction
{
    private $templatePath = __DIR__ . '/../ui/template/account.html';
    private $templatePathInSetup = __DIR__ . '/../ui/template/in-setup.html';
    private $title = 'account';

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
                $this->translate($this->title, 'gambio_connect_general'),
                $this->templatePathInSetup,
            );
            return $response->write($template);
        }

        if (!$this->moduleStatusService->isSetUp()) {
            return $response->withRedirect($this->url->admin() . '/makaira/gambio-connect', 302);
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
            'makairaLink' => rtrim($this->moduleConfigService->getMakairaUrl(), "/") . 'MakairaConnectAccount.php/' . 'admin/' .  $this->moduleConfigService->getMakairaInstance(),
            'makairaUrl' => $this->moduleConfigService->getMakairaUrl(),
            'makairaInstance'  => $this->moduleConfigService->getMakairaInstance(),
            'cornStatus' => (bool)$this->moduleConfigService->getCronjobStatus() ? $this->translate('corn_status_1', 'gambio_connect_general') : $this->translate('corn_status_0', 'gambio_connect_general'),
            'queueLength' => $this->changesService->getQueueLength(),
            'lastSync' => 'test',
            'recoCrossSelling' =>  $this->moduleConfigService->getRecoCrossSelling(),
            'recoReversCrossSelling' => $this->moduleConfigService->getRecoReverseCrossSelling(),
        ];
    }
    private function handleGet(Request $request, Response $response): Response
    {

        $template = $this->render(
            $this->translate($this->title, 'gambio_connect_general'),
            $this->templatePath,
            $this->getData()
        );

        return $response->write($template);
    }

    private function handlePost(Request $request, Response $response): Response
    {

        $invalid = [];
        $requestData = $request->getParsedBody();

        $recoCrossSelling = htmlspecialchars($requestData['recoCrossSelling']);
        v::stringType()->validate($recoCrossSelling) ? $this->moduleConfigService->setRecoCrossSelling($recoCrossSelling) : $invalid[] = 'makairaInstance';

        $recoReversCrossSelling = htmlspecialchars($requestData['recoReversCrossSelling']);
        v::stringType()->validate($recoReversCrossSelling) ? $this->moduleConfigService->setRecoReverseCrossSelling($recoReversCrossSelling) : $invalid[] = 'recoReversCrossSelling';


        $dataValidation = [
            'recoCrossSelling' =>  $recoCrossSelling,
            'recoReversCrossSelling' => $recoReversCrossSelling,
            'validationErrors' => $invalid,
            'notification' => $this->getNotification($invalid),
        ];

        $template = $this->render(
            $this->translate($this->title, 'gambio_connect_general'),
            $this->templatePath,
            array_merge($this->getData(), $dataValidation)
        );

        return $response->write($template);
    }

    private function getNotification(array $invalid): array
    {
        if (empty($invalid)) {
            return ['type' => 'success', 'message' => $this->translate('saved', 'gambio_connect_general'), 'title' => $this->translate('success', 'gambio_connect_general')];
        }

        return ['type' => 'warning', 'message' => $this->translate('invalid', 'gambio_connect_general'), 'title' => $this->translate('warning', 'gambio_connect_general')];
    }
}
