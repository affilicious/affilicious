<?php

class TestAffiliciousPlugin extends WP_UnitTestCase
{
    /**
     * @convers AffiliciousPlugin::registerPublicHooks
     */
    public function testRegisterPublicHooks()
    {
        $plugin = $this->getMockBuilder('AffiliciousPlugin')
            ->setMethods(array('registerPublicHooks'))
            ->getMock();

        $plugin->expects($this->once())
            ->method('registerPublicHooks');

        $plugin->run();
    }

    /**
     * @convers AffiliciousPlugin::registerAdminHooks
     */
    public function testRegisterAdminHooks()
    {
        $plugin = $this->getMockBuilder('AffiliciousPlugin')
            ->setMethods(array('registerAdminHooks'))
            ->getMock();

        $plugin->expects($this->once())
            ->method('registerAdminHooks');

        $plugin->run();
    }
}
