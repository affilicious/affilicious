<?php
namespace Affilicious\Common\Generator;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Slug_Generator_Interface
{
    /**
     * Create a new slug from the name.
     *
     * @since 0.8
     * @param Name $name
     * @return Slug
     */
    public function generate_from_name(Name $name);
}
