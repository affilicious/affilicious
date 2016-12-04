<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Attribute\Domain\Exception\Duplicated_Attribute_Template_Exception;
use Affilicious\Attribute\Domain\Model\Attribute\Attribute_Template;
use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Entity;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Domain\Model\Update_Aware_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template_Group extends Abstract_Entity implements Update_Aware_Interface
{
    /**
     * There is a limit of 20 characters for post types in Wordpress
     */
    const POST_TYPE = 'aff_attr_template';

	/**
     * The unique ID of the attribute template group
     * Note that you just get the ID in Wordpress, if you store a post.
     *
	 * @var Attribute_Template_Group_Id
	 */
	protected $id;

    /**
     * The title of the attribute template group for display usage
     *
     * @var Title
     */
    protected $title;

    /**
     * The unique name of the attribute template group for url usage
     *
     * @var Name
     */
    protected $name;

    /**
     * The key of the attribute template group for database usage
     *
     * @var Key
     */
    protected $key;

    /**
     * Holds all attributes templates to build the concrete attributes
     *
     * @var Attribute_Template[]
     */
    protected $attribute_templates;

    /**
     * The date and time of the last update.
     *
     * @var \DateTimeImmutable
     */
    protected $updated_at;

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
        $this->attribute_templates = array();
        $this->updated_at = new \DateTimeImmutable('now');
    }

    /**
     * Check if the attribute template group has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * Get the optional attribute template group ID
     *
     * @since 0.6
     * @return null|Attribute_Template_Group_Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the optional attribute template group ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|Attribute_Template_Group_Id $id
     */
    public function set_id($id)
    {
        if($id !== null && !($id instanceof Attribute_Template_Group_Id)) {
            throw new Invalid_Type_Exception($id, 'Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Id');
        }

        $this->id = $id;
    }

    /**
     * Get the title for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Get the unique name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Set the unique name for the url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function set_name(Name $name)
    {
        $this->name = $name;
    }

	/**
     * Get the key for the database usage
     *
     * @since 0.6
	 * @return Key
	 */
	public function get_key()
	{
		return $this->key;
	}

    /**
     * Set the unique key for database usage
     *
     * @since 0.6
     * @param Key $key
     */
    public function set_key(Key $key)
    {
        $this->key = $key;
    }

    /**
     * Check if a attribute template with the given name exists
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function has_attribute_template(Name $name)
    {
        return isset($this->attribute_templates[$name->get_value()]);
    }

    /**
     * Add a new attribute template
     *
     * @since 0.6
     * @param Attribute_Template $attribute_template
     */
    public function add_attribute_template(Attribute_Template $attribute_template)
    {
        if($this->has_attribute_template($attribute_template->get_name())) {
            throw new Duplicated_Attribute_Template_Exception($attribute_template, $this);
        }

        $this->attribute_templates[$attribute_template->get_name()->get_value()] = $attribute_template;
    }

    /**
     * Remove an existing attribute template by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function remove_attribute_template(Name $name)
    {
        unset($this->attribute_templates[$name->get_value()]);
    }

    /**
     * Get an existing attribute template by the name
     * You don't need to check for the name, but you will get null on non-existence
     *
     * @since 0.6
     * @param Name $name
     * @return null|Attribute_Template
     */
    public function get_attribute(Name $name)
    {
        if($this->has_attribute_template($name)) {
            return $this->attribute_templates[$name->get_value()];
        }

        return null;
    }

    /**
     * Get all attribute templates
     *
     * @since 0.6
     * @return Attribute_Template[]
     */
    public function get_attribute_templates()
    {
        $attribute_templates = array_values($this->attribute_templates);

        return $attribute_templates;
    }

    /**
     * Set all attribute templates
     * If you do this, the old templates going to be replaced.
     *
     * @since 0.6
     * @param Attribute_Template[] $attribute_templates
     */
    public function set_attribute_templates($attribute_templates)
    {
        $this->attribute_templates = array();

    	// add_attribute_template checks for the type
    	foreach ($attribute_templates as $attribute) {
    		$this->add_attribute_template($attribute);
	    }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_updated_at()
    {
        return clone $this->updated_at;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_updated_at(\DateTimeImmutable $updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * Get the raw post
     *
     * @since 0.6
     * @return null|\WP_Post
     */
    public function get_raw_post()
    {
        if(!$this->has_id()) {
            return null;
        }

        return get_post($this->id->get_value());
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            ($this->has_id() && $this->get_id()->is_equal_to($object->get_id()) || !$object->has_id()) &&
            $this->get_title()->is_equal_to($object->get_title()) &&
            $this->get_name()->is_equal_to($object->get_name()) &&
            $this->get_updated_at() == $object->get_updated_at();
            // TODO: Compare the rest and check the best way to compare two arrays with objects inside
    }
}
