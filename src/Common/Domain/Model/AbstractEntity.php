<?php
namespace Affilicious\Common\Domain\Model;

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
