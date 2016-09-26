<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class AbstractEntity implements EntityInterface
{
	/**
	 * @var ValueObjectInterface
	 */
	protected $id;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
}
