<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Model\AbstractEntity;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Exception\DuplicatedDetailTemplateException;
use Affilicious\Detail\Domain\Model\DetailTemplate\DetailTemplate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DetailTemplateGroup extends AbstractEntity
{
    const POST_TYPE = 'detail_group';

	/**
     * The unique ID of the detail template group
     * Note that you just get the ID in Wordpress, if you store a post.
     *
	 * @var DetailTemplateGroupId
	 */
	protected $id;

    /**
     * The title of the detail template group for display usage
     *
     * @var Title
     */
    protected $title;

    /**
     * The unique name of the detail template group for url usage
     *
     * @var Name
     */
    protected $name;

    /**
     * The key of the detail template group for database usage
     *
     * @var Key
     */
    protected $key;

    /**
     * Holds all detail templates to build the concrete details
     *
     * @var DetailTemplate[]
     */
    protected $detailTemplates;

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
        $this->detailTemplates = array();
    }

    /**
     * Check if the detail template group has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasId()
    {
        return $this->id !== null;
    }

    /**
     * Get the detail template group ID
     *
     * @since 0.6
     * @return DetailTemplateGroupId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the detail template group ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|DetailTemplateGroupId $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the title for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the unique name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the unique name for url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function setName(Name $name)
    {
        $this->name = $name;
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
     * Check if a detail template with the given name exists
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function hasDetailTemplate(Name $name)
    {
        return isset($this->detailTemplates[$name->getValue()]);
    }

    /**
     * Add a new detail template
     *
     * @since 0.6
     * @param DetailTemplate $detailTemplate
     * @throws DuplicatedDetailTemplateException
     */
    public function addDetailTemplate(DetailTemplate $detailTemplate)
    {
        if($this->hasDetailTemplate($detailTemplate->getName())) {
            throw new DuplicatedDetailTemplateException($detailTemplate, $this);
        }

        $this->detailTemplates[$detailTemplate->getKey()->getValue()] = $detailTemplate;
    }

    /**
     * Remove an existing detail template by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function removeDetailTemplate(Name $name)
    {
        unset($this->detailTemplates[$name->getValue()]);
    }

    /**
     * Get an existing detail template by the name
     * You don't need to check for the name, but you will get null on non-existence
     *
     * @since 0.6
     * @param Name $name
     * @return null|DetailTemplate
     */
    public function getDetailTemplate(Name $name)
    {
        if($this->hasDetailTemplate($name)) {
            return $this->detailTemplates[$name->getValue()];
        }

        return null;
    }

    /**
     * Get all detail templates
     *
     * @since 0.6
     * @return DetailTemplate[]
     */
    public function getDetailTemplates()
    {
        $detailTemplates = array_values($this->detailTemplates);

        return $detailTemplates;
    }

    /**
     * Set all detail templates
     * If you do this, the old templates going to be replaced.
     *
     * @since 0.6
     * @param DetailTemplate[] $detailTemplates
     */
    public function setDetailTemplates($detailTemplates)
    {
        $this->detailTemplates = array();

    	// addDetailTemplate checks for the type
    	foreach ($detailTemplates as $detail) {
    		$this->addDetailTemplate($detail);
	    }
    }

    /**
     * Get the raw post
     *
     * @since 0.6
     * @return null|\WP_Post
     */
    public function getRawPost()
    {
        if(!$this->hasId()) {
            return null;
        }

        return get_post($this->id->getValue());
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getId()->isEqualTo($object->getId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName());
            // TODO: Compare the rest and check the best way to compare two arrays with objects inside
    }
}
