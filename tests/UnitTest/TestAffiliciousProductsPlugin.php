<?php

class TestAffiliciousProductsPlugin extends WP_UnitTestCase
{
    /**
     * @convers AffiliciousProductsPlugin::registerPublicHooks
     */
    public function testRegisterPublicHooks()
    {
        $plugin = $this->getMockBuilder('AffiliciousProductsPlugin')
            ->setMethods(array('registerPublicHooks'))
            ->getMock();

        $plugin->expects($this->once())
            ->method('registerPublicHooks');

        $plugin->run();
    }

    /**
     * @convers AffiliciousProductsPlugin::registerAdminHooks
     */
    public function testRegisterAdminHooks()
    {
        $plugin = $this->getMockBuilder('AffiliciousProductsPlugin')
            ->setMethods(array('registerAdminHooks'))
            ->getMock();

        $plugin->expects($this->once())
            ->method('registerAdminHooks');

        $plugin->run();
    }
}
