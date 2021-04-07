<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created By : Rohan Hapani
 */
namespace Mageplaza\GiftCard\Model;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 2;

    public function toOptionArray()
    {
        return [
            [
                self::STATUS_ENABLED => __('Enabled'),
                self::STATUS_DISABLED => __('Disabled'),
            ],
        ];
    }

    public static function getOptionArray()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled'),
        ];
    }
}
