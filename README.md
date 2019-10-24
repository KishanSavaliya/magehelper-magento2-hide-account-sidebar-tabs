# MageHelper Hide Sidebar Tabs for different types of customer groups.

We can create new module in `app/code/` directory, previously in Magento 1 there were three code pools which are local, community and core but that has been removed now.

In this blog post we will see how to hide sidebar tabs for different types of customer groups in Magento 2, you can download this module as well for practice.

### Step - 1 - Create a directory for the module

- In Magento 2, module name divided into two parts i.e Vendor_Module (for e.g Magento_Theme, Magento_Catalog)
- We will create `MageHelper_HideSidebarTabs` here, So `MageHelper` is vendor name and `HideSidebarTabs` is name of this module.
- So first create your namespace directory (`MageHelper`) and move into that directory.
- Then create module name directory (`HideSidebarTabs`)

Now Go to : `app/code/MageHelper/HideSidebarTabs`

### Step - 2 - Create module.xml file to declare new module.

- Magento 2 looks for configuration information for each module in that module’s etc directory. so we need to add module.xml file here in our module `app/code/MageHelper/HideSidebarTabs/etc/module.xml` and it's content for our module is :

~~~ xml
<?xml version="1.0"?>
<!--
/**
 * MageHelper Hide Sidebar Tabs for different types of customer groups.
 *
 * @package      MageHelper_HideSidebarTabs
 * @author       Kishan Savaliya <kishansavaliyakb@gmail.com>
 */
-->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
	<module name="MageHelper_HideSidebarTabs" setup_version="1.0.0" />
</config>
~~~

In this file, we register a module with name `MageHelper_HideSidebarTabs` and the version is `1.0.0`.

### Step - 3 - create registration.php

- All Magento 2 module must be registered in the Magento system through the magento `ComponentRegistrar` class. This file will be placed in module's root directory.

In this step, we need to create this file:

~~~
app/code/MageHelper/HideSidebarTabs/registration.php
~~~

And it’s content for our module is:

~~~ php
<?php
/**
 * MageHelper Hide Sidebar Tabs for different types of customer groups.
 *
 * @package      MageHelper_HideSidebarTabs
 * @author       Kishan Savaliya <kishansavaliyakb@gmail.com>
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'MageHelper_HideSidebarTabs',
    __DIR__
);
~~~

### Step - 4 - Enable `MageHelper_HideSidebarTabs` module.

- By finish above step, you have created an empty module. Now we will enable it in Magento environment.
- Before enable the module, we must check to make sure Magento has recognize our module or not by enter the following at the command line:

~~~ 
php bin/magento module:status
~~~

If you follow above step, you will see this in the result:

~~~
List of disabled modules:
MageHelper_HideSidebarTabs
~~~

This means the module has recognized by the system but it is still disabled. Run this command to enable it:

~~~
php bin/magento module:enable MageHelper_HideSidebarTabs
~~~

The module has enabled successfully if you saw this result:

~~~
The following modules has been enabled:
- MageHelper_HideSidebarTabs
~~~

- We will not run `php bin/magento setup:upgrade` this command here we will run this command later in next step.

### Step - 5 - Create Block and layout files.

We will first create `customer_account.xml` file here

> app/code/MageHelper/HideSidebarTabs/view/frontend/layout/customer_account.xml

Content for this file is ..

~~~
<?xml version="1.0"?>
<!-- 
/**
 * MageHelper Hide Sidebar Tabs for different types of customer groups.
 *
 * @package      MageHelper_HideSidebarTabs
 * @author       Kishan Savaliya <kishansavaliyakb@gmail.com>
 */
-->
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceBlock name="customer_account_navigation">
            <block class="MageHelper\HideSidebarTabs\Block\Account\WishlistLink" ifconfig="wishlist/general/active" name="customer-account-navigation-wish-list-link">
                <arguments>
                    <argument name="path" xsi:type="string">wishlist</argument>
                    <argument name="label" xsi:type="string" translate="true">My Wish List</argument>
                    <argument name="sortOrder" xsi:type="number">210</argument>
                </arguments>
            </block>
        </referenceBlock>
    </body>
</page>
~~~

Now we will create Block file here on this location

> app/code/MageHelper/HideSidebarTabs/Block/Account/WishlistLink.php

Content for this file is ..

~~~
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
~~~

That's it. Now you need to run below commands..

~~~
php bin/magento setup:upgrade
php bin/magento cache:clean
php bin/magento cache:flush
~~~