<?php
namespace Affilicious\Common\Domain\Model;

interface RepositoryInterface
{
    /**
     * Find all entities
     * @return array[]
     */
    public function findAll();
}
