<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractAggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Exception\DuplicatedDetailException;
use Affilicious\Detail\Domain\Model\Detail\Detail;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DetailGroup extends AbstractAggregate
{
    /**
     * This ID is the same as the related template
     *
     * @var DetailTemplateGroupId
     */
    protected $templateId;

    /**
     * @var Title
     */
    protected $title;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Key
     */
    protected $key;

    /**
     * @var Detail[]
     */
    protected $details;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
    public function __construct(Title $title, Name $name, Key $key)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->details = array();
    }

    /**
     * Check if the detail group has a template ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasTemplateId()
    {
        return $this->templateId !== null;
    }

    /**
     * Get the detail group template ID
     *
     * @since 0.6
     * @return null|DetailTemplateGroupId
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Set the detail group template ID
     *
     * @since 0.6
     * @param null|DetailTemplateGroupId $templateId
     * @throws InvalidTypeException
     */
    public function setTemplateId($templateId)
    {
        if($templateId !== null && !($templateId instanceof DetailTemplateGroupId)) {
            throw new InvalidTypeException($templateId, 'Affilicious\Detail\Domain\Model\DetailTemplateGroupId');
        }

        $this->templateId = $templateId;
    }

    /**
     * Get the title
     *
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the key for database usage
     *
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Check if a detail with the given name exists
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function hasDetail(Name $name)
    {
        return isset($this->details[$name->getValue()]);
    }

    /**
     * Add a new detail
     *
     * @since 0.6
     * @param Detail $detail
     * @throws DuplicatedDetailException
     */
    public function addDetail(Detail $detail)
    {
        /*
        if($this->hasDetail($detail->getName())) {
            throw new DuplicatedDetailException($detail, $this);
        }
        */

        $this->details[$detail->getName()->getValue()] = $detail;
    }

    /**
     * Remove an existing detail by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function removeDetail(Name $name)
    {
        unset($this->details[$name->getValue()]);
    }

    /**
     * Get an existing detail by the name
     * You don't need to check for the name, but you will get null on non-existence
     *
     * @since 0.6
     * @param Name $name
     * @return null|Detail
     */
    public function getDetail(Name $name)
    {
        if($this->hasDetail($name)) {
            return $this->details[$name->getValue()];
        }

        return null;
    }

    /**
     * Get all details
     *
     * @since 0.6
     * @return Detail[]
     */
    public function getDetails()
    {
        $details = array_values($this->details);

        return $details;
    }

    /**
     * Set all details
     *
     * @since 0.6
     * @param Detail[] $details
     */
    public function setDetails($details)
    {
        $this->details = array();

        // addDetail checks for the type
        foreach ($details as $detail) {
            $this->addDetail($detail);
        }
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            ($this->hasTemplateId() && $this->getTemplateId()->isEqualTo($object->getTemplateId()) || !$object->hasTemplateId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName()) &&
            $this->getKey()->isEqualTo($object->getKey());
            // TODO: A good way to compare two arrays with objects
    }
}
