<?php
namespace Ec\Qr\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Filters info model
 */
class EcOrder  extends \Magento\Framework\Model\AbstractModel
    implements  IdentityInterface
{

    /**#@-*/

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'ecqr_order';

    /**
     * @var string
     */
    protected $_cacheTag = 'ecqr_order';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ecqr_order';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ec\Qr\Model\ResourceModel\EcOrder');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}
