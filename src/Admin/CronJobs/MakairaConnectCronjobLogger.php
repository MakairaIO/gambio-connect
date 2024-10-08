<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class MakairaConnectCronjobLogger extends AbstractCronjobLogger
{
    /**
     * {@inheritDoc}
     */
    public function log(array $context = [])
    {
        if (! empty($context['message']) && ! empty($context['level'])) {
            $this->logger->log($context['level'], $context['message']);
        } else {
            $this->logger->info('MakairaConnect PL export', $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function logError(array $context = [])
    {
        if (! empty($context['message'])) {
            $this->logger->error($context['message']);
        } else {
            $this->logger->error('MakairaConnect PL export error', $context);
        }
    }
}
