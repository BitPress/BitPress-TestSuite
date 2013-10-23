<?php

//version 0.9

class TestSuite
{
	private $runner;
	private $tests;
	
	public function __construct()
	{
		$this->runner = new TestRunner();
		$this->tests = array();
	}
	
	public function register( $test )
	{
		array_push( $this->tests, $test );
	}
	
	public function run()
	{
		while( count( $this->tests ) )
			$this->runner->testClass( array_shift( $this->tests ) );	
	}	
}

class TestRunner
{
	var $logger;
	
	public function __construct()
	{
		$this-> logger = new Logger();		
	}
	
	public function testClass( $test )	
	{		
		$this->logger->classInfo( $test );				
		
		$methods = get_class_methods( $test );
		
		for( $i = 0, $len = count( $methods ); $i < $len; $i++ )
			if (strpos( $methods[ $i ], 'test') === 0 )				
				$this->testMethod( $test, $methods[ $i ] );		
	}
	
	public function testMethod( $test, $method )
	{
		$test->setup();
		
		try {
			
			$test->$method();
			
			$this->logger->success( $method );	
					
		} catch (Exception $e) {		
			
			$this->logger->fail( $method, $e->getMessage() );				
		}
		
		$test->teardown();
	}	
}

class Logger
{
	public function classInfo( $test )
	{
		$this->logMessage( 'Class: '.get_class( $test ) );
	}
	
	public function success( $method )
	{
		$this->logMethod( $method );
		$this->logMessage( " " );				
	}
	
	public function fail( $method, $err )
	{
		$this->logMethod( $method );
		$this->logError( $err );
		$this->logMessage( " " );		
	}
	
	private function logMethod( $method )
	{
		$methodInfo = explode("_", $method );
		
		$this->logMessage( "  Method: " . $methodInfo[ 1 ] );
		$this->logMessage( "  Scenario: " . $this->beautifyCamelCase( $methodInfo[ 2 ] ) );
		$this->logMessage( "  Result: " . $this->beautifyCamelCase( $methodInfo[ 3 ] ) );		
	}

	private function logMessage( $message )
	{
		echo( '<script>console.log("'.$message.'");</script>');
	}
	
	private function logError( $message )
	{
		echo( '<script>console.warn("'.$message.'");</script>');		
	}
	
	private function beautifyCamelCase( $string )
	{
		$string = preg_replace('/([A-Z])/', ' $1', $string);
		$string = strtolower( $string );
		
		return $string;
	}
}



abstract class Test
{
	public $name;
	
	abstract public function setup();
	
	abstract public function teardown();

	protected function assertTrue( $condition )
	{
		if ( !$condition )
			throw new Exception('condition was not true' );
	}

	protected function assertFalse( $condition )
	{
		if ( $condition )
			throw new Exception('condition was not false' );
	}
	
	protected function assertEquals( $expected, $actual )
	{
		if( $expected !== $actual )
			throw new Exception('expected '.$expected.', actual '.$actual);		
	}
	
	protected function assertNotEquals( $expected, $actual )
	{
		if( $expected === $actual )
			throw new Exception('expected and actual were equal');
	}
	
	protected function fail()
	{
		throw new Exception('method failed manually');
	}
}

?>