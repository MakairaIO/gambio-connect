<?php

namespace Makaira\Query;

interface MachineLearningSupport
{
    /**
     * Determines if machine learning is enabled for this query.
     *
     * @return bool
     */
    public function isMachineLearningEnabled();
}
