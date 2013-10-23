//Version 0.9

function TestSuite()
{
	this.runner = new TestRunner();
	this.tests = new Array();
	
	this.register = function( test )
	{
		this.tests.push( test );
	};
	
	this.run = function()
	{
		while( this.tests.length )
			this.runner.testClass( this.tests.shift() );		
	};
}

function TestRunner()
{
	var logger = new Logger();
	
	this.testClass = function( test )
	{
		
		console.log( "Class: " + test.constructor.name);
		
		for( var method in test )
			if ( method.indexOf( "test") == 0 )
				testMethod( test, method );				    	
	};	
	
	function testMethod( test, method )
	{
		test.setup();
		
		try{
						
			test[ method ]();
			
			logger.success( method );
			
		}catch( err )
		{
			
			logger.fail(  method, err );
			
		};		
		
		test.teardown();
	};		
}

function Logger()
{
	this.success = function( method, message )
	{
		logMethod( method );
		
		console.log( " " );
	};
	
	this.fail = function( method, message )
	{
		logMethod( method );
		logMessage( message );

		console.log( " " );
	};
	
	function logMethod( method )
	{
		var methodInfo = method.split( "_" );
		
		console.log( "  Method: "+ methodInfo[ 1 ] );
		console.log( "  Scenario: "+ beautifyCamelCase( methodInfo[ 2 ] ) );
		console.log( "  Result: "+ beautifyCamelCase( methodInfo[ 3 ] ) );		
	}
	
	function beautifyCamelCase( string ) 
	{
		string = string.replace(/([a-z])([A-Z])/g, '$1 $2');
		string = string.toLowerCase();
		
		return string;
	}
	
	function logMessage( message )
	{
		console.warn( "Failed: "+message );
	}

}

function Test()
{
	this.setup = function(){};
	this.teardown = function(){};

	this.assertTrue = function( condition )
	{
		if ( !condition )
			throw "condition was not true";
	};	
	this.assertFalse = function( condition )
	{
		if ( condition )
			throw "condition was not false";
	};	
	
	this.assertEquals = function( expected, actual )
	{
		if( expected !== actual )
			throw "expected " + expected + ", actual " + actual;
	};
	
	this.assertNotEquals = function( expected, actual )
	{
		if( expected === actual )
			throw "expected and actual were equal";
	};	
	
	this.fail = function()
	{
		throw "method failed manually";
	};
}

