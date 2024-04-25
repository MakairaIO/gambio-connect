<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira;

final class Constraints
{
    /**
     * Use stock information to filter search results.
     */
    const USE_STOCK = 'query.use_stock';

    /**
     * Filter search result by category id.
     */
    const CATEGORY = 'query.category_id';

    /**
     * Filter search result by manufacturer id.
     */
    const MANUFACTURER = 'query.manufacturer_id';

    /**
     * Filter products by attached/related datatype. This requires also Constraints::DATA_IDS (see below).
     */
    const DATA_TYPE = 'query.related_datatype';

    /**
     * Filter products by attached/related datatype ids. This requires also Constraints::DATA_TYPE (see above).
     */
    const DATA_IDS = 'query.query.related_datatype_ids';

    /**
     * Use given datatypes for search.
     */
    const DATATYPES = 'query.datatypes';

    /**
     * Only search in the specified shop.
     */
    const SHOP = 'query.shop_id';

    /**
     * Language used in this query.
     */
    const LANGUAGE = 'query.language';

    /**
     * Allow every search reasult using this query.
     */
    const EVERYTHING = 'query.everything';

    /**
     * Variant count used in query.
     */
    const VARIANT_COUNT = 'query.variant_count';

    /**
     * Variant sorting used in query.
     */
    const VARIANT_SORTING = 'query.variant_sorting';

    /**
     * Variant count used in query.
     */
    const MAX_RESULTS = 'query.max_results';

    /**
     * Attributes that should be in response.
     */
    const ATTRIBUTE_IDS = 'query.attribute_ids';

    /**
     *  Use machine learning in this query.
     */
    const MACHINE_LEARNING = 'query.machine_learning';

    /**
     *  Use data of specified group in this query.
     */
    const GROUP = 'query.group';

    /**
     *  Don't change the keys to lowercase
     */
    const ORIGINAL_KEYS = 'query.original_keys';

    /**
     * Vendor used in this query.
     */
    const VENDOR = 'vendor';

    /**
     * OI User-ID used in this query.
     */
    const OI_USER_ID = 'oi.user.id';

    /**
     * OI User-IP used in this query.
     */
    const OI_USER_IP = 'oi.user.ip';

    /**
     * OI User-Agent-String used in this query.
     */
    const OI_USER_AGENT = 'oi.user.agent';

    /**
     * OI User-Timezone used in this query.
     */
    const OI_USER_TIMEZONE = 'oi.user.timezone';
    /**
     * Array of product ids to exclude from result
     */
    const PLACED_PRODUCT_IDS = 'placed_ids.product';

    /**
     * Array of product ids used for aggragation
     */
    const PLACED_PRODUCT_IDS_AGGS = 'placed_ids.product.aggs';

    /**
     * Array of category ids to exclude from result
     */
    const PLACED_CATEGORY_IDS = 'placed_ids.category';

    /**
     * Array of manufacturer ids to exclude from result
     */
    const PLACED_MANUFACTURER_IDS = 'placed_ids.manufacturer';

    /**
     * key of personalization to use
     */
    const PERSONALIZATION_TYPE = 'personalization.type';

    /**
     * Array of session data to use for resort the elastic result
     */
    const PERSONALIZATION_DATA = 'personalization.data';

    /**
     * Array of active experiments for A/B testing, which were received from Makaira.
     */
    const AB_EXPERIMENTS = 'ab.experiments';

    /**
     * Array of ranking mix configurations that will be used to override the current configurations.
     */
    const RANKING_MIX_OVERRIDING_CONFIG = 'ranking_mix_overriding_config';

    /**
     * Array of weather groups that will be used to build makaira boost fields
     */
    const WEATHER_GROUPS = 'weather_groups';

    /**
     * Use data for specific persona in this query.
     */
    const PERSONA = 'query.persona';
}
