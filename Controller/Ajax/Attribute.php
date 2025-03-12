<?php

namespace Bhavy\AjaxFilter\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Eav\Model\Config;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Attribute
 *
 * Handles the fetching of attribute values and types for products.
 */
class Attribute extends Action
{
    /**
     * @var Config
     */
    protected $eav;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Attribute constructor.
     *
     * @param Context $context
     * @param Config $eav
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Config $eav,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->eav = $eav;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Executes the attribute fetching functionality.
     *
     * Fetches attribute values based on the provided attribute code and returns
     * them in JSON format.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $attributecode = $this->_request->getParam('attribute');
        $attributeType = '';
        $values = [];

        if (!empty($attributecode)) {
            $attribute = $this->eav->getAttribute('catalog_product', $attributecode);

            if ($attribute) {
                $attributeType = $attribute->getFrontendInput();

                // Handle select or multiselect attributes
                if ($attributeType == 'select' || $attributeType == 'multiselect') {
                    $attributeValues = $attribute->getSource()->getAllOptions();
                    foreach ($attributeValues as $attributeVal) {
                        if ($attributeVal['value'] !== '') {
                            $values[] = [
                                'value' => $attributeVal['value'],
                                'label' => $attributeVal['label']
                            ];
                        }
                    }
                } elseif ($attributeType == 'boolean') {
                    $values = [
                        ['value' => '1', 'label' => __('Yes')],
                        ['value' => '0', 'label' => __('No')]
                    ];
                } elseif ($attributeType == 'text' || $attributeType == 'textarea') {
                    $values = [];
                }
            }
        }

        return $result->setData([
            'attributeval' => $values,
            'attributetype' => $attributeType
        ]);
    }
}
