<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\UrlRewriteGenerator\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator as CatalogCategoryUrlRewriteGenerator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Byte8\Core\Framework\DataStorageInterface;
use Byte8\Core\Framework\DataStorageInterfaceFactory;
use Byte8\Core\Framework\MessageCollectorInterface;
use Byte8\Core\Framework\MessageCollectorInterfaceFactory;
use Byte8\Core\Model\Source\StatusInterface;
use function implode;

/**
 * @inheritDoc
 */
class CategoryUrlRewriteGenerator implements UrlRewriteInterface
{
    /**
     * @var DataStorageInterface
     */
    private DataStorageInterface $responseStorage;

    /**
     * @var MessageCollectorInterface
     */
    private MessageCollectorInterface $messageCollector;

    /**
     * @var array
     */
    private array $urlInMemory = [];

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CatalogCategoryUrlRewriteGenerator $categoryUrlRewriteGenerator
     * @param DataStorageInterfaceFactory $dataStorageFactory
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param MessageCollectorInterfaceFactory $messageCollectorFactory
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private CatalogCategoryUrlRewriteGenerator $categoryUrlRewriteGenerator,
        DataStorageInterfaceFactory $dataStorageFactory,
        private MergeDataProviderFactory $mergeUrlDataProviderFactory,
        MessageCollectorInterfaceFactory $messageCollectorFactory,
        private UrlPersistInterface $urlPersist
    ) {
        $this->responseStorage = $dataStorageFactory->create();
        $this->messageCollector = $messageCollectorFactory->create();
    }

    /**
     * @return DataStorageInterface
     */
    public function getResponseStorage(): DataStorageInterface
    {
        return $this->responseStorage;
    }

    /**
     * @return MessageCollectorInterface
     */
    public function getMessageCollector(): MessageCollectorInterface
    {
        return $this->messageCollector;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $entityIds, ?int $storeId = null): void
    {
        if (empty($entityIds)) {
            return;
        }

        $this->initialize();

        foreach ($entityIds as $entityId) {
            $category = null;

            try {
                $category = $this->categoryRepository->get($entityId);
                $this->generate($category);
                $this->getResponseStorage()->addData($entityId);
                $this->getMessageCollector()->addMessage(
                    $entityId,
                    __(
                        'Url rewrites have been generated. [Category: %1, Store: %2]',
                        $entityId,
                        implode(', ', $category->getStoreIds() ?: '')
                    ),
                    StatusInterface::SUCCESS
                );
            } catch (\Exception $e) {
                $this->getMessageCollector()->addMessage(
                    $entityId,
                    __(
                        'Could not generate URL rewrites. [Category: %1, Store: %2, Error: %3]',
                        $entityId,
                        $category ? implode(', ', $category->getStoreIds() ?: '') : 0,
                        $e->getMessage()
                    ),
                    StatusInterface::ERROR
                );
            }
        }
    }

    /**
     * @return void
     */
    private function initialize(): void
    {
        $this->responseStorage->resetData();
        $this->messageCollector->reset();
    }

    /**
     * @param CategoryInterface $category
     * @return void
     * @throws NoSuchEntityException
     * @throws UrlAlreadyExistsException
     */
    private function generate(CategoryInterface $category): void
    {
        $mergeUrlDataProvider = $this->mergeUrlDataProviderFactory->create();

        foreach ($category->getStoreIds() ?: [] as $storeId) {
            $storeId = (int) $storeId;
            if ($storeId === Store::DEFAULT_STORE_ID) {
                continue;
            }

            /** @var Category|CategoryInterface $category */
            $category = $this->categoryRepository->get($category->getEntityId(), $storeId);
            $urlRewrites = $this->categoryUrlRewriteGenerator->generate($category, true);

            foreach (array_keys($urlRewrites) as $index) {
                if (isset($this->urlInMemory[$index])) {
                    unset($urlRewrites[$index]);
                } else {
                    $this->urlInMemory[$index] = true;
                }
            }
            $mergeUrlDataProvider->merge($urlRewrites);
        }

        if ($urlData = $mergeUrlDataProvider->getData()) {
            $this->urlPersist->replace($urlData);
        }
    }
}
