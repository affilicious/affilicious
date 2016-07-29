<?php
namespace Affilicious\ProductsPlugin\Product\Detail;

use Affilicious\ProductsPlugin\Exception\InvalidOptionException;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class Detail
{
    const ID_TEMPLATE = 'at_product_details_%s_%s';
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_FILE = 'file';

    public static $types = array(
        self::TYPE_TEXT => self::TYPE_TEXT,
        self::TYPE_NUMBER => self::TYPE_NUMBER,
        self::TYPE_FILE => self::TYPE_FILE,
    );

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $label;

    /**
     * @var null|string
     */
    private $value;

    /**
     * @param string $key
     * @param string $type
     * @param string $label
     * @param null|string $value
     * @throws \Exception
     */
    public function __construct($key, $type, $label, $value = null)
    {
        $this->key = $key;
        $this->type = $type;
        $this->label = $label;
        $this->value = $value;

        $this->assertValidType($type);
    }

    /**
     * Get the key identifier
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get the type (text, number, file)
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Check if the detail is a text
     * @return bool
     */
    public function isText()
    {
        return $this->getType() === 'text';
    }

    /**
     * Check if the detail is a number
     * @return bool
     */
    public function isNumber()
    {
        return $this->getType() === 'number';
    }

    /**
     * Check if the detail is a file
     * @return bool
     */
    public function isFile()
    {
        return $this->getType() === 'file';
    }

    /**
     * Get the raw label
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function hasValue()
    {
        return $this->value !== null;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return null|string
     */
    public function getDownloadLink()
    {
        if (!$this->isFile()) {
            return null;
        }

        $fileId = $this->getValue();
        $fileUrl = \wp_get_attachment_image_url($fileId);

        if (empty($fileUrl)) {
            return null;
        }

        return $fileUrl;
    }

    /**
     * Throw an error if the type is invalid
     * @param string $type
     * @throws \Exception
     */
    private function assertValidType($type)
    {
        if(!isset(self::$types[$type])) {
            throw new InvalidOptionException($type, self::$types);
        }
    }
}
