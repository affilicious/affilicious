<?php

class TestAffilicious_Plugin extends WP_UnitTestCase
{
    /**
     * @convers Affilicious_Plugin::registerPublicHooks
     */
    public function testRegisterPublicHooks()
    {
        $plugin = $this->getMockBuilder('Affilicious_Plugin')
            ->setMethods(array('registerPublicHooks'))
            ->getMock();

        $plugin->expects($this->once())
            ->method('registerPublicHooks');

        $plugin->run();
    }

    /**
     * @convers Affilicious_Plugin::registerAdminHooks
     */
    public function testRegisterAdminHooks()
    {
        $plugin = $this->getMockBuilder('Affilicious_Plugin')
            ->setMethods(array('registerAdminHooks'))
            ->getMock();

        $plugin->expects($this->once())
            ->method('registerAdminHooks');

        $plugin->run();
    }
}
