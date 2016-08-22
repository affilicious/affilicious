<?php
namespace Affilicious\Common\Domain\Model;

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
