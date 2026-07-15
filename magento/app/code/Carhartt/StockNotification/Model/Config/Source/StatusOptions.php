<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Carhartt\StockNotification\Model\ResourceModel\Notification\CollectionFactory;

/**
 * StatusOptions class
 */
class StatusOptions implements ArrayInterface
{
    /**
     * StatusOptions constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        protected CollectionFactory $collectionFactory)
    {}

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('status');
        $collection->getSelect()->group('status');

        foreach ($collection as $item) {
            $status = $item->getData('status');
            $options[] = ['value' => $status, 'label' => $status];
        }
        return $options;
    }
}
