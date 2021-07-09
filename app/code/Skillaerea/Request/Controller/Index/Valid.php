<?php
declare(strict_types=1);

namespace Skillaerea\Request\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 */
class Valid extends Action
{
    public function execute()
    {
        /** @var  $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $page;
    }
}
