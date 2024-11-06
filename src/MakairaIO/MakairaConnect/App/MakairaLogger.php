<?php

declare(strict_types=1);

namespace GXModules\MakairaIO\MakairaConnect\App;

use Psr\Log\LoggerInterface;

use function Gambio\Core\Logging\logger;

/**
 * Class MakairaLogger
 */
class MakairaLogger implements LoggerInterface
{
    public const LOGFILE = 'makaira';

    private const LOG_LEVELS = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

    private LoggerInterface $logger;

    /**
     * MakairaLogger constructor.
     */
    public function __construct()
    {
        $this->logger = logger(self::LOGFILE);
    }

    /**
     * {@inheritDoc}
     */
    public function emergency($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function alert($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function critical($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function error($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function warning($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function notice($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function info($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function debug($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = []): void
    {
        $messageLevel = (int) array_search($level, self::LOG_LEVELS, true);

        if ($messageLevel >= 0) {
            $this->logger->log($level, $message, $context);
        }
    }
}
