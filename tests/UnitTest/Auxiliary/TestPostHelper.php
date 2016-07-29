<?php
use Affilicious\ProductsPlugin\Auxiliary\PostHelper;

class TestPostHelper extends WP_UnitTestCase
{
    /**
     * @covers PostHelper::getPost
     */
    public function testGetPost()
    {
        $postId = $this->factory->post->create();

        // ID is int
        $comparePost = PostHelper::getPost(intval($postId));
        $this->assertInstanceOf('WP_Post', $comparePost);

        // ID is string
        $comparePost = PostHelper::getPost(strval($postId));
        $this->assertInstanceOf('WP_Post', $comparePost);

        // ID is object
        $comparePost = PostHelper::getPost(get_post($postId));
        $this->assertInstanceOf('WP_Post', $comparePost);
    }
}
