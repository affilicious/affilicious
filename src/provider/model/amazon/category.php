<?php
namespace Affilicious\Provider\Model\Amazon;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Category
{
	use Simple_Value_Trait {
		Simple_Value_Trait::__construct as private set_value;
	}

	/**
	 * List of all available search indices in Germany.
	 *
	 * @var array
	 */
	public static $germany = [
		'All' => 'Alle Kategorien',
		'Apparel' => 'Bekleidung',
		'Appliances' => 'Elektro-Großgeräte',
		'Automotive' => 'Auto & Motorrad',
		'Baby' => 'Baby',
		'Beauty' => 'Beauty',
		'Books' => 'Bücher',
		'Classical' => 'Klassik',
		'DVD' => 'DVD & Blu-ray',
		'Electronics' => 'Elektronik & Foto',
		'ForeignBooks' => 'Fremdsprachige Bücher',
		'GiftCards' => 'Geschenkgutscheine',
		'Grocery' => 'Lebensmittel & Getränke',
		'Handmade' => 'Handgemacht',
		'HealthPersonalCare' => 'Drogerie & Körperpflege',
		'HomeGarden' => 'Garten',
		'Industrial' => 'Technik & Wissenschaft',
		'Jewelry' => 'Schmuck',
		'KindleStore' => 'Kindle-Shop',
		'Kitchen' => 'Küche & Haushalt',
		'Lighting' => 'Beleuchtung',
		'Luggage' => 'Koffer, Rucksäcke & Taschen',
		'Magazines' => 'Zeitschriften',
		'MobileApps' => 'Apps & Spiele',
		'MP3Downloads' => 'Musik-Downloads',
		'Music' => 'Musik-CDs & Vinyl',
		'MusicalInstruments' => 'Musikinstrumente & DJ-Equipment',
		'OfficeProducts' => 'Bürobedarf & Schreibwaren',
		'Pantry' => 'Amazon Pantry',
		'PCHardware' => 'Computer & Zubehör',
		'PetSupplies' => 'Haustier',
		'Photo' => 'Kamera & Foto',
		'Shoes' => 'Schuhe & Handtaschen',
		'Software' => 'Software',
		'SportingGoods' => 'Sport & Freizeit',
		'Tools' => 'Baumarkt',
		'Toys' => 'Spielzeug',
		'UnboxVideo' => 'Amazon Instant Video',
		'VideoGames' => 'Games',
		'Watches' => 'Uhren',
	];

	/**
	 * @since 0.9.7
	 * @param string $value
	 */
	public function __construct($value)
	{
		Assert_Helper::is_string_not_empty($value, __METHOD__, 'Expected category to be a non empty string. Got: %s', '0.9.7');

		$this->set_value($value);
	}
}
