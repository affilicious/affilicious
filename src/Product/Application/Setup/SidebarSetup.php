<?php
namespace Affilicious\ProductsPlugin\Product\Application\Setup;

use Affilicious\ProductsPlugin\Product\Application\Sidebar\MainSidebar;
use Affilicious\ProductsPlugin\Product\Application\Sidebar\ProductSidebar;

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
     * @since 0.3
     */
    public function init()
    {
        $this->mainSidebar->init();
        $this->productSidebar->init();
    }

    /**
     * @inheritdoc
     * @since 0.3
     */
    public function render()
    {
    }
}
