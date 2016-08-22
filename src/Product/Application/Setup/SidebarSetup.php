<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Product\Application\Sidebar\MainSidebar;
use Affilicious\Product\Application\Sidebar\ProductSidebar;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class SidebarSetup implements SetupInterface
{
    /**
     * @var MainSidebar
     */
    private $mainSidebar;

    /**
     * @var ProductSidebar
     */
    private $productSidebar;

    /**
     * @since 0.3
     */
    public function __construct()
    {
        $this->mainSidebar = new MainSidebar();
        $this->productSidebar = new ProductSidebar();
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->mainSidebar->init();
        $this->productSidebar->init();
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
    }
}
