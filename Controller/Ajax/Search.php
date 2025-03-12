<?php
namespace Bhavy\AjaxFilter\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Helper\Image;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Search extends Action
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
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Configurable
     */
    protected $configurableType;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Search constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Image $imageHelper
     * @param Configurable $configurableType
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        Image $imageHelper,
        Configurable $configurableType,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
    }

    /**
     * Executes the search functionality.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $searchTerm = $this->getRequest()->getParam('term');
        
        $response = [
            'exactMatch' => null,
            'partialMatches' => []
        ];
        
        if (!empty($searchTerm)) {
            // First check for exact match
            $exactCollection = $this->productCollectionFactory->create();
            $exactCollection->addAttributeToSelect(['name', 'sku', 'entity_id', 'url_key', 'thumbnail']);
            $exactCollection->addAttributeToFilter('sku', $searchTerm);
            // $exactCollection->addUrlRewrite();
            $exactCollection->setPageSize(1);
            
            if ($exactCollection->getSize() > 0) {
                $exactProduct = $exactCollection->getFirstItem();
                $imageUrl = $this->imageHelper->init($exactProduct, 'product_thumbnail_image')
                    ->setImageFile($exactProduct->getThumbnail())
                    ->resize(100, 100)
                    ->getUrl();
                
                $response['exactMatch'] = [
                    'id' => $exactProduct->getId(),
                    'sku' => $exactProduct->getSku(),
                    'name' => $exactProduct->getName(),
                    'url' => $exactProduct->getProductUrl(),
                    'image' => $imageUrl
                ];
            } else {
                // Look for partial matches
                $partialCollection = $this->productCollectionFactory->create();
                $partialCollection->addAttributeToSelect(['name', 'sku', 'entity_id', 'url_key', 'type']);
                $partialCollection->addAttributeToFilter('sku', ['like' => '%' . $searchTerm . '%']);
                // $partialCollection->addUrlRewrite();
                $partialCollection->addAttributeToFilter('visibility', 4);

                foreach ($partialCollection as $product) {
                    $response['partialMatches'][] = [
                        'id' => $product->getId(),
                        'sku' => $product->getSku(),
                        'name' => $product->getName(),
                        'url' => $product->getProductUrl(),
                        'type' => $product->getType()
                    ];
                }
            }
        }
        
        return $result->setData($response);
    }
}
