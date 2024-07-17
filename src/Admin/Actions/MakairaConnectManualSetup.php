<?php

declare(strict_types=1);

namespace GXModules\MakairaIO\MakairaConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use Respect\Validation\Validator as v;

/**s
 * Class MakairaConnectManualSetup
 *
 * @package GXModules\MakairaIO\MakairaConnect\Admin\Actions
 */
class MakairaConnectManualSetup extends AdminModuleAction
{
    private $templatePath = __DIR__ . '/../ui/template/manual-setup.html';
    private $title = 'manual_setup';


    public function __construct(protected ModuleConfigService $moduleConfigService)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {


        if ($request->isPost()) {
            return $this->handlePost($request, $response);
        }

        return $this->handleGet($request, $response);
    }

    private function handleGet(Request $request, Response $response): Response
    {


        $template = $this->render(
            $this->translate($this->title, 'makaira_connect_general'),
            $this->templatePath,
            [
                'gambio_connect_form_url' => $this->url->admin(),
                'makairaUrl' => $this->moduleConfigService->getMakairaUrl(),
                'makairaInstance'  => $this->moduleConfigService->getMakairaInstance(),
                'makairaSecret' => $this->moduleConfigService->getMakairaSecret(),
            ]
        );

        return $response->write($template);
    }

    private function handlePost(Request $request, Response $response): Response
    {

        $invalid = [];
        $requestData = $request->getParsedBody();

        $makairaUrl = htmlspecialchars($requestData['makairaUrl']);
        v::url()->validate($makairaUrl) ? $this->moduleConfigService->setMakairaUrl($makairaUrl) : $invalid[] = 'makairaUrl';

        $makairaInstance = htmlspecialchars($requestData['makairaInstance']);
        v::stringType()->validate($makairaInstance) ? $this->moduleConfigService->setMakairaInstance($makairaInstance) : $invalid[] = 'makairaInstance';

        $makairaSecret = htmlspecialchars($requestData['makairaSecret']);
        v::stringType()->validate($makairaSecret) ? $this->moduleConfigService->setMakairaSecret($makairaSecret) : $invalid[] = 'makairaSecret';


        $template = $this->render(
            $this->translate($this->title, 'makaira_connect_general'),
            $this->templatePath,
            [
                'gambio_connect_form_url' => $this->url->admin(),
                'makairaUrl' => $makairaUrl,
                'makairaInstance'  => $makairaInstance,
                'makairaSecret' => $makairaSecret,
                'validationErrors' => $invalid,
                'notification' => $this->getNotification($invalid),
            ]
        );

        return $response->write($template);
    }

    private function getNotification(array $invalid): array
    {
        if (empty($invalid)) {
            return ['type' => 'success', 'message' => $this->translate('saved', 'makaira_connect_general'), 'title' => $this->translate('success', 'makaira_connect_general')];
        }

        return ['type' => 'warning', 'message' => $this->translate('invalid', 'makaira_connect_general'), 'title' => $this->translate('warning', 'makaira_connect_general')];
    }
}
