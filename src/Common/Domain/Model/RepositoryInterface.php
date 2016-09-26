<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface RepositoryInterface
{
    /**
     * Find all entities
     *
     * @since 0.3
     * @return array[]
     */
    public function findAll();
}
