<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Entity;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Exception\Duplicated_Detail_Template_Exception;
use Affilicious\Detail\Domain\Model\Detail\Detail_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail_Template_Group extends Abstract_Entity
{
    /**
     * There is a limit of 20 characters for post types in Wordpress
     * TODO: Change the post type to 'aff_detail_template' before the beta release
     */
    const POST_TYPE = 'detail_group';

	/**
     * The unique ID of the detail template group
     * Note that you just get the ID in Wordpress, if you store a post.
     *
	 * @var Detail_Template_Group_id
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
     * @var Detail_Template[]
     */
    protected $detail_templates;

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
        $this->detail_templates = array();
    }

    /**
     * Check if the detail template group has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * Get the optional detail template group ID
     *
     * @since 0.6
     * @return null|Detail_Template_Group_id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the optional detail template group ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|Detail_Template_Group_id $id
     */
    public function set_id($id)
    {
        if($id !== null && !($id instanceof Detail_Template_Group_id)) {
            throw new Invalid_Type_Exception($id, 'Affilicious\Detail\Domain\Model\Detail_Template_Group_id');
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
     * Set the unique name for url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function set_name(Name $name)
    {
        $this->name = $name;
    }

	/**
     * Get the key for database usage
     *
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
     * Check if a detail template with the given name exists
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function has_detail_template(Name $name)
    {
        return isset($this->detail_templates[$name->get_value()]);
    }

    /**
     * Add a new detail template
     *
     * @since 0.6
     * @param Detail_Template $detail_template
     * @throws Duplicated_Detail_Template_Exception
     */
    public function add_detail_template(Detail_Template $detail_template)
    {
        if($this->has_detail_template($detail_template->get_name())) {
            throw new Duplicated_Detail_Template_Exception($detail_template, $this);
        }

        $this->detail_templates[$detail_template->get_key()->get_value()] = $detail_template;
    }

    /**
     * Remove an existing detail template by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function remove_detail_template(Name $name)
    {
        unset($this->detail_templates[$name->get_value()]);
    }

    /**
     * Get an existing detail template by the name
     * You don't need to check for the name, but you will get null on non-existence
     *
     * @since 0.6
     * @param Name $name
     * @return null|Detail_Template
     */
    public function get_detail_template(Name $name)
    {
        if($this->has_detail_template($name)) {
            return $this->detail_templates[$name->get_value()];
        }

        return null;
    }

    /**
     * Get all detail templates
     *
     * @since 0.6
     * @return Detail_Template[]
     */
    public function get_detail_templates()
    {
        $detail_templates = array_values($this->detail_templates);

        return $detail_templates;
    }

    /**
     * Set all detail templates
     * If you do this, the old templates going to be replaced.
     *
     * @since 0.6
     * @param Detail_Template[] $detail_templates
     */
    public function set_detail_templates($detail_templates)
    {
        $this->detail_templates = array();

    	// add_detail_template checks for the type
    	foreach ($detail_templates as $detail) {
    		$this->add_detail_template($detail);
	    }
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
            $this->get_name()->is_equal_to($object->get_name());
            // TODO: Compare the rest and check the best way to compare two arrays with objects inside
    }
}
