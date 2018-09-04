# NullStream

A PHP stream wrapper equivalent to `/dev/null` - a stream that almost
guaranteed not to fail on `fopen`, that you can write to without it 
storing anything and that returns only empty strings when you read from it.

## Usage
This class can be used in places where you need to have a stream resource
for type safety, but where using a *real* resource would be cumbersome. For example 

* as a fallback stream to guarantee a resource return type 
* in a unit test where a function needs a resource, as a test double.

```PHP
stream_wrapper_register( 'null', Birke\NullStream\NullStream::class );

function fopenWithGuaranteedResource( string $name, string $mode ) {
    $res = fopen( $name, $mode );
    if ( $res === false ) {
        return fopen( 'null:///', $mode );
    }
    return $res;
}

assert( is_resource( fopenWithGuaranteedResource( 'foo.txt', 'r' ) ) );
 
```

```PHP
use Birke\NullStream\NullStream;
use PHPUnit\Framework\TestCase;
use My\Logger;
use My\Service;

class ServiceIntegrationTest extends TestCase
{

    public static function setUpBeforeClass()
    {
        if ( in_array( 'null', stream_get_wrappers() ) ) {
            stream_wrapper_unregister( 'null' );
        }
        stream_wrapper_register( 'null', NullStream::class );
    }
    
    public function testServiceReturnsHello() {
        $service = new Service( new Logger( 'null://service.log' ) );
        
        $this->assertSame( 'hello', $service->sayHello() );
    }
}
 
```
