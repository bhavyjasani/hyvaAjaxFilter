<?php
namespace Bhavy\AjaxFilter\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Eav\Model\Config;

class SkuSearch extends Template
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * SkuSearch constructor.
     *
     * @param Context $context
     * @param Config $eavConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $eavConfig,
        array $data = []
    ) {
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get all custom product attributes.
     *
     * @return array
     */
    public function getAllProductAttributes()
    {
        $attributes = $this->_eavConfig->getEntityType('catalog_product')->getAttributeCollection();
        
        $allAttributes = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getIsUserDefined()) {
                $allAttributes[] = [
                    'attribute_code' => $attribute->getAttributeCode(),
                ];
            }
        }

        return $allAttributes;
    }
}
