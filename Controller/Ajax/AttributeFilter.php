<?php

namespace Bhavy\AjaxFilter\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class AttributeFilter
 *
 * Controller for handling attribute-based product filtering via AJAX.
 */
class AttributeFilter extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Configurable
     */
    protected $configurableType;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * AttributeFilter constructor.
     *
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param JsonFactory $resultJsonFactory
     * @param Configurable $configurableType
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        JsonFactory $resultJsonFactory,
        Configurable $configurableType,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
    }

    /**
     * Executes the attribute filter functionality.
     *
     * Filters products based on attributes and returns the results in JSON format.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $attributecode = $this->_request->getParam('attribute');
        $values = $this->_request->getParam('value');
        $attributecode2 = $this->_request->getParam('attribute2');
        $values2 = $this->_request->getParam('value2');

        // Build product collection based on the filters
        if (!empty($values2) && !empty($values)) {
            $value2 = explode(',', $values2);
            $value = explode(',', $values);
            $collections = $this->productCollectionFactory->create();
            $collections->addAttributeToFilter($attributecode, ['in' => [$value]])
                ->addAttributeToFilter($attributecode2, ['in' => [$value2]]);
            $collections->addAttributeToSelect(['name', 'sku', 'entity_id', 'url_key', 'type']);
        } elseif (!empty($values2)) {
            $value2 = explode(',', $values2);
            $collections = $this->productCollectionFactory->create();
            $collections->addAttributeToFilter($attributecode2, ['in' => [$value2]]);
            $collections->addAttributeToSelect(['name', 'sku', 'entity_id', 'url_key', 'type']);
        } else {
            $value = explode(',', $values);
            $collections = $this->productCollectionFactory->create();
            $collections->addAttributeToFilter($attributecode, ['in' => [$value]]);
            $collections->addAttributeToSelect(['name', 'sku', 'entity_id', 'url_key', 'type']);
        }

        // Prepare response data
        $response = ['products' => []];
        foreach ($collections as $collection) {
            $response['products'][] = [
                'id' => $collection->getId(),
                'sku' => $collection->getSku(),
                'name' => $collection->getName(),
                'type' => $collection->getType(),
                'url' => $collection->getProductUrl()
            ];
        }

        return $result->setData($response);
    }

    // Example of commented-out code
    // protected function getProductData($product)
    // {
    //     $productId = $product->getId();
    //     $finalProduct = $product;
    //     $isChild = false;
    //     // Check if this is a simple product that is part of a configurable
    //     if ($product->getTypeId() == 'simple') {
    //         $parentIds = $this->configurableType->getParentIdsByChild($productId);
    //         if (!empty($parentIds)) {
    //             try {
    //                 $parent = $this->productRepository->getById($parentIds[0]);
    //                 $finalProduct = $parent;
    //                 $isChild = true;
    //             } catch (\Exception $e) {
    //                 // If parent can't be loaded, use the original product
    //             }
    //         }
    //     }
    //     return $finalProduct->getProductUrl();
    // }
}
