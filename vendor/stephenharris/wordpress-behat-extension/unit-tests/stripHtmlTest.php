<?php 

class stripHtmlTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider htmlProvider
	 */
	function testStripHTML( $html, $expected ) {

		$mock = $this->getMockForTrait('\StephenHarris\WordPressBehatExtension\StripHtml');

		$actual = $mock->stripTagsAndContent($html);
		$this->assertEquals( $expected, $actual );
	}


	public function htmlProvider()
	{
		return array(
			'basic' => array( '<b>text</b> with <div>tags</div>', 'with' ),
			'tag with attributes' => array( 'Some text <span class="some class">inside a tag</span>', 'Some text' ),
			'nested tags' => array( 'Some text <span><span>inside two tags</span></span>', 'Some text' ),
			'real example' => array( 'Comments <span class="awaiting-mod count-0"><span class="pending-count">0</span></span>', 'Comments' ),
			'empty string' => array( '', '' ),
			'invalid tags' => array( 'Invalid > HTML', 'Invalid > HTML' ),
			'wrapped in tag' => array( '<strong>wrapped in a tag</strong>', '' ),
		);
	}

}