<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * @version 0.1
 * @author  Stefan Krenz <krenz@marmalade.de>
 * @link    http://www.marmalade.de
 */

namespace Makaira;

use Makaira\Exceptions\DomainException;
use Makaira\Query\MachineLearningSupport;

class RecommendationQuery extends AbstractQuery implements MachineLearningSupport
{
    /**
     * @var string
     */
    public $recommendationId;

    /**
     * @var string
     */
    public $requestId;

    /**
     * @var array|string
     */
    public $productId;

    /**
     * @var string
     * @deprecated
     */
    public $categoryId;

    /**
     * @var string
     * @deprecated
     */
    public $manufacturerId;

    /**
     * @var string
     * @deprecated
     */
    public $priceRangeMin;

    /**
     * @var string
     * @deprecated
     */
    public $priceRangeMax;

    /**
     * @var string
     * @deprecated
     */
    public $timeRangeMin;

    /**
     * @var string
     * @deprecated
     */
    public $timeRangeMax;

    /**
     * @var string[]
     * @deprecated
     */
    public $attributes;

    /**
     * @var string[]
     * @deprecated
     */
    public $filterAttributes;

    /**
     * @var array
     */
    public $boosting;

    /**
     * @var array
     */
    public $filter;

    /**
     * @var boolean
     */
    public $randomness;

    /**
     * @var boolean
     */
    public $isUseMachineLearning;

    /**
     * @var boolean
     */
    public $isUseRankingMix;

    /**
     * @var string
     */
    public $recommendationType;

    /**
     * @var boolean
     */
    public $isActive;

    /**
     * @var boolean
     */
    public $isPopulateTextSimilarity;
    
    /**
     * @var boolean
     */
    public $boostWithSimilarAttributes;
    
    /**
     * @var boolean
     */
    public $diversify;

    /**
     * @return string[]
     */
    public function getMandatoryConstraints()
    {
        return [
            Constraints::LANGUAGE => 'language',
            Constraints::SHOP => 'shop identifier',
        ];
    }

    /**
     * @throws DomainException
     */
    public function verify()
    {
        parent::verify();

        if (null === $this->count) {
            throw new DomainException('count: must be set!');
        }

        if (1 > $this->count) {
            throw new DomainException('count: must be greater than 0!');
        }

        foreach (['filter', 'boosting'] as $setting) {
            if (!empty($this->$setting) && (!is_array($this->$setting) || !is_array($this->$setting[0]))) {
                throw new DomainException($setting . ': must be an array of ' . $setting . 's!');
            } elseif (is_array($this->$setting)) {
                $hasInvalidStructure = count(array_filter($this->$setting, function ($structure) {
                        return empty($structure['field']) || empty($structure['operator']);
                })) > 0;

                if ($hasInvalidStructure) {
                    throw new DomainException($setting . ': must have at least "field" and "operator" set!');
                }
            }
        }
        return $this;
    }

    /**
     * Determines if machine learning is enabled for this query.
     *
     * @return bool
     */
    public function isMachineLearningEnabled()
    {
        return (bool) $this->isUseMachineLearning;
    }
}
