<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\UrlRewriteGenerator\Model;

use Byte8\Core\Framework\DataStorageInterface;
use Byte8\Core\Framework\MessageCollectorInterface;

/**
 * Interface UrlRewriteInterface used to
 * generate URL rewrites for a given entity.
 */
interface UrlRewriteInterface
{
    /**
     * @return DataStorageInterface
     */
    public function getResponseStorage(): DataStorageInterface;

    /**
     * @return MessageCollectorInterface
     */
    public function getMessageCollector(): MessageCollectorInterface;

    /**
     * @param array $entityIds
     * @param int|null $storeId
     * @return void
     */
    public function execute(array $entityIds, ?int $storeId = null): void;
}
