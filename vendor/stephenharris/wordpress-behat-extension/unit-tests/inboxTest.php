<?php 
use \StephenHarris\WordPressBehatExtension\WordPress\InboxFactory;

class inboxTest extends PHPUnit_Framework_TestCase
{
	
	public function setUp(){
		$this->inboxFactory = new InboxFactory(WORDPRESS_FAKE_MAIL_DIR);
	}
	
	public function tearDown(){
		$files = glob(WORDPRESS_FAKE_MAIL_DIR.'/*'); // get all file names
		foreach($files as $file){ // iterate files
			if(is_file($file))
				unlink($file); // delete file
		}
	}
	
	protected function expectException( $class, $message, $code = null ) {
		self::setExpectedException( $class, $message, $code );
	}
	
	
	public function testEmailRecieved(){
		
		$this->sendEmail( 'test@example.com', 'Subject', 'Message' );
		
		$inbox        = $this->inboxFactory->getInbox( 'test@example.com' );
		$email        = $inbox->getLatestEmail();

		$this->assertEquals( 'Subject', $email->getSubject() );
		$this->assertEquals( 'Message', $email->getBody() );
		$this->assertEquals( 'test@example.com', $email->getRecipient() );
		
	}
	
	public function testInboxCleared(){		
		$this->sendEmail( 'test@example.com', 'Subject', 'Message' );
	
		$inbox        = $this->inboxFactory->getInbox( 'test@example.com' );
		$inbox->getLatestEmail();
		
		$this->expectException( '\Exception', 'Inbox for test@example.com is empty' );
		$inbox->clearInbox();
		
		//Inbox is clear, we should get an exception
		$inbox->getLatestEmail();
	}
	
	public function testSelectEmailBySubject(){
		$this->sendEmail( 'test@example.com', 'Foo', 'First' );
		$this->sendEmail( 'test@example.com', 'Bar', 'Second' );
		$this->sendEmail( 'test@example.com', 'Foo', 'Third' );
		$this->sendEmail( 'test@example.com', 'Bar', 'Fourth' );
	
		$inbox        = $this->inboxFactory->getInbox( 'test@example.com' )->refresh();
		$email        = $inbox->getLatestEmail('Foo');
	
		$this->assertEquals( 'Foo', $email->getSubject() );
		$this->assertEquals( 'Third', $email->getBody() );
		$this->assertEquals( 'test@example.com', $email->getRecipient() );
	}	
	
	public function testSortedEmails(){
		
		$this->sendEmail( 'test@example.com', 'First', 'Between', '1465302020' );
		$this->sendEmail( 'test@example.com', 'Second', 'Latest', '1465302030' );
		$this->sendEmail( 'test@example.com', 'Third', 'Earlier', '1465302010' );
		
		$inbox        = $this->inboxFactory->getInbox( 'test@example.com' )->refresh();
		$email        = $inbox->getLatestEmail();
	
		$this->assertEquals( 'Second', $email->getSubject() );
		$this->assertEquals( 'Latest', $email->getBody() );
	}
	
	private function sendEmail( $to, $subject = '', $message = '', $timestamp = null ) {

		$dir       = rtrim(WORDPRESS_FAKE_MAIL_DIR, DIRECTORY_SEPARATOR);
		$timestamp = is_null($timestamp) ? time() : (int) $timestamp;
		$fileName  = $timestamp . "-$to-" . $subject;
		$filePath = $dir . DIRECTORY_SEPARATOR . $fileName;

		$data = array(
			'to'      => $to,
			'subject' => $subject,
			'message' => $message
		);
		if (!is_dir(WORDPRESS_FAKE_MAIL_DIR)) {
			mkdir(WORDPRESS_FAKE_MAIL_DIR, 0777, true);
		}

		$result = (bool) file_put_contents($filePath, json_encode($data));
		return $result;
	}
	
	
}