<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Ui\DataProvider\Notification;

use Carhartt\StockNotification\Model\ResourceModel\Notification\CollectionFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\DocumentInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    public $collection;
    public $searchCriteria;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly DocumentInterfaceFactory $documentFactory,
        private readonly AttributeValueFactory $attributeValueFactory,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    public function getSearchCriteria(): SearchCriteriaInterface
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
        }
        return $this->searchCriteria;
    }

    public function getSearchResult(): SearchResultsInterface
    {
        $searchCriteria = $this->getSearchCriteria();

        $this->collection
            ->setCurPage($searchCriteria->getCurrentPage() ?? 1)
            ->setPageSize($searchCriteria->getPageSize() ?? 200);

        $documents = array_map(
            fn ($item) => $this->convertToDocument($item),
            array_values($this->collection->getItems())
        );

        return $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria)
            ->setItems($documents)
            ->setTotalCount($this->collection->getSize());
    }

    public function getData(): array
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items'        => array_values($this->getCollection()->getData()),
        ];
    }

    private function convertToDocument($item): DocumentInterface
    {
        $document = $this->documentFactory->create();
        $document->setId($item->getId());

        $attributes = [];
        foreach ($item->getData() as $key => $value) {
            $attribute = $this->attributeValueFactory->create();
            $attribute->setAttributeCode($key);
            $attribute->setValue($value);
            $attributes[$key] = $attribute;
        }

        $document->setCustomAttributes($attributes);
        return $document;
    }
}
