<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\UrlRewriteGenerator\Model\UrlRewriteImport\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Byte8\UrlRewriteGenerator\Model\UrlRewriteImportInterface;

/**
 * @inheritDoc
 */
class TargetPath extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function isValid($value): bool
    {
        $this->_clearMessages();

        if (empty($value[UrlRewriteImportInterface::COLUMN_NO_TARGET_PATH])) {
            $this->_addMessages([
                __('Missing value for column: "%1"', UrlRewrite::TARGET_PATH)
            ]);
            return false;
        }

        return true;
    }
}
