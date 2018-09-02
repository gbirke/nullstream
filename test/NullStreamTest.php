<?php

declare( strict_types = 1 );

namespace Birke\Nullstream\Test;

use Birke\NullStream\NullStream;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

class NullStreamTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        if ( in_array( 'null', stream_get_wrappers() ) ) {
            stream_wrapper_unregister( 'null' );
        }
        stream_wrapper_register( 'null', NullStream::class );
    }

    public static function tearDownAfterClass()
    {
        stream_wrapper_unregister( 'null' );
    }

    public function testStreamCanBeOpened() {
        $stream = fopen( 'null://foo', 'r' );

        $this->assertNotFalse( $stream, 'Stream fopen should return a resource' );
    }

    public function testStreamCanBeWrittenTo() {
        $stream = fopen( 'null://foo', 'w' );

        $this->assertNotFalse( fwrite( $stream, 'Content' ) );
    }

    public function testWritesAlwaysZeroBytesRegardlessOfDataSize() {
        $stream = fopen( 'null://foo', 'w' );

        $this->assertSame( 0, fwrite( $stream, 'Content' ) );
        $this->assertSame( 0, fwrite( $stream, str_repeat( 'Content', 500 ) ) );
    }

    public function testReadsAlwaysEmptyString() {
        $stream = fopen( 'null://foo', 'r' );

        $this->assertSame( '', fread( $stream, 1024 ) );
        $this->assertSame( '', fread( $stream, 2048 ) );
    }

    public function testStreamIsAlwaysEof() {
        $stream = fopen( 'null://foo', 'r' );

        $this->assertTrue( feof( $stream ) );
    }

    public function testStreamStoresNothing() {
        $stream = fopen( 'null://foo', 'w' );
        fwrite( $stream, 'Content' );
        rewind( $stream );

        $this->assertTrue( feof( $stream ) );
        $this->assertSame( '', fread( $stream, 1024 ) );
    }

    /**
     * @dataProvider seekPositionProvider
     * @param int $seekPosition
     */
    public function testStreamIsSeekableToAnyPosition( int $seekPosition ) {
        $stream = fopen( 'null://foo', 'r' );

        $this->assertSame( 0, fseek( $stream, $seekPosition ) );
    }

    public function seekPositionProvider() {
        yield [ 100 ];
        yield [ 999999 ];
        yield [ -1 ];
    }

    /**
     * @dataProvider seekPositionProvider
     * @param int $seekPosition
     */
    public function testStreamAlwaysReturnsZeroAsPositionAfterSeeking( int $seekPosition ) {
        $stream = fopen( 'null://foo', 'r' );

        fseek( $stream, $seekPosition );

        $this->assertSame( 0, ftell( $stream ) );
    }

    public function testStreamCannotTruncate() {
        $stream = fopen( 'null://foo', 'r' );

        $this->expectException( Warning::class );
        $this->assertFalse( ftruncate( $stream, 100 ) );
    }

    public function testStreamCanBeFlushed() {
        $stream = fopen( 'null://foo', 'w' );

        fwrite( $stream, 'Content' );

        $this->assertTrue( fflush( $stream ) );
    }

    public function testStreamCannotBeRenamed() {
        $this->assertFalse( rename( 'null://foo', 'null://bar' ) );
    }

    /**
     * @dataProvider lockTypesProvider
     * @param int $lockType
     */
    public function testStreamCannotBeLocked( int $lockType ) {
        $stream = fopen( 'null://foo', 'r' );

        $this->assertFalse( flock( $stream, $lockType ) );
    }

    public function lockTypesProvider()
    {
        yield [ LOCK_EX ];
        yield [ LOCK_SH ];
        yield [ LOCK_UN ];
    }

    public function testStreamCannotBeDeleted() {
        $this->assertFalse( unlink( 'null://foo' ) );
    }

    public function testStreamDoesNotSupportMetadata() {
        $this->assertFalse( touch( 'null://foo' ) );
        $this->assertFalse( chmod( 'null://foo', 0667 ) );
        $this->assertFalse( chown( 'null://foo', 'root' ) );
        $this->assertFalse( chgrp( 'null://foo', 'root' ) );
    }

    public function testStreamDoesNotSupportStat() {
        $stream = fopen( 'null://foo', 'r' );

        $this->expectException( Warning::class );
        fstat( $stream );
    }

    public function testStreamCanBeClosed() {
        $stream = fopen( 'null://foo', 'r' );

        $this->assertTrue( fclose( $stream ) );
    }

}
