<?php

namespace StephenHarris\WordPressBehatExtension\Context\Page;

class EditPostPage extends AdminPage
{

    protected $path = '/wp-admin/post.php?post={id}&action=edit';

    /**
     * @param array $urlParameters
     */
    protected function verifyPage()
    {
        $this->assertHasHeader('Edit Post');
    }
}
