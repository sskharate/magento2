<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog;

use Magento\Framework\Exception\LocalizedException;

class Delete extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface $ruleRepository */
                $ruleRepository = $this->_objectManager->get(
                    \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface::class
                );
                $ruleRepository->deleteById($id);

                $this->_objectManager->create(\Magento\CatalogRule\Model\Flag::class)->loadSelf()->setState(1)->save();
                $this->messageManager->addSuccess(__('You deleted the rule.'));
                $this->_redirect('catalog_rule/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete this rule right now. Please review the log and try again.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_redirect('catalog_rule/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a rule to delete.'));
        $this->_redirect('catalog_rule/*/');
    }
}
