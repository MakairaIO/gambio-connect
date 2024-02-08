<?php

class GambioConnectCronjobLogger extends AbstractCronjobLogger
{

    /**
     * @inheritDoc
     */
    public function log(array $context = [])
    {
        if (!empty($context['message']) && !empty($context['level'])) {
            $this->logger->log($context['level'], $context['message']);
        } else {
            $this->logger->info('GambioConnect PL export', $context);
        }
    }

    /**
     * @inheritDoc
     */
    public function logError(array $context = [])
    {
        if (!empty($context['message'])) {
            $this->logger->error($context['message']);
        } else {
            $this->logger->error('GambioConnect PL export error', $context);
        }
    }
}