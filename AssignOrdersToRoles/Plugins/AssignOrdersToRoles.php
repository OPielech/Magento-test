<?php namespace Solwin\AssignOrdersToRoles\Plugins;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;

class AssignOrdersToRoles
{
    private $messageManager;
    private $collection;
    protected  $adminSession;

    public function __construct(MessageManager $messageManager,
        SalesOrderGridCollection $collection,
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {

        $this->messageManager = $messageManager;
        $this->collection = $collection;
        $this->adminSession = $adminSession;            
    }

    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == 'sales_order_grid_data_source') {
             $current_adminuser =   $this->adminSession->getUser()->getAclRole();
             if(4 == $current_adminuser){
                  if ($result instanceof $this->collection) {
                      $this->collection->addFieldToFilter('status', array('in' => array('accepted')));
                  }
              }
             return $this->collection;
        }
        return $result;

    }
}