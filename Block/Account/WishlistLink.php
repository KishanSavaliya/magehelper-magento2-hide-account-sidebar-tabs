<?php
/**
 * MageHelper Hide Sidebar Tabs for different types of customer groups.
 *
 * @package      MageHelper_HideSidebarTabs
 * @author       Kishan Savaliya <kishansavaliyakb@gmail.com>
 */
 
namespace MageHelper\HideSidebarTabs\Block\Account;

class WishlistLink extends \Magento\Framework\View\Element\Html\Link\Current
{
    protected $_customerSession;

    protected $customerGroup;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        array $data = []
     ) {
         $this->_customerSession = $customerSession;
         $this->customerGroup = $customerGroup;
         parent::__construct($context, $defaultPath, $data);
     }

    protected function _toHtml()
    {
        $customerGroupId = $this->_customerSession->getCustomerGroupId();
        $customerGroups = $this->customerGroup->toOptionArray();

        foreach ($customerGroups as $customerGroup) {
            if($customerGroup['value'] == $customerGroupId){
                $currentCustomerGroup = $customerGroup['label'];
            }
        }

        if($this->_customerSession->isLoggedIn()) {
            //if(in_array($currentCustomerGroup, array("B2C Customer", "B2C Employee"))) {
            if(in_array($currentCustomerGroup, array("B2C Customer"))) { // I've added 'B2C Customer' here in Condition so 'My Wishlist link only visible for B2C Customer customer group's customers only.
                return parent::_toHtml();
            } else {
                return; 
            }
        }
        return;
    }
}