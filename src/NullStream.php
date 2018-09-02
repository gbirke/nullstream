<?php

declare( strict_types = 1 );

namespace Birke\NullStream;

class NullStream {

    public function stream_open( string $url, string $mode, int $options,  &$opened_path ): bool {
        return true;
    }

    public function stream_write( string $data ): int {
        return 0;
    }

    public function stream_read( int $length ) {
        return '';
    }

    public function stream_eof(): bool {
        return true;
    }

    public function stream_seek( int $offset , int $whence = SEEK_SET ): bool {
        return true;
    }

    public function stream_tell(): int {
        return 0;
    }

    public function stream_flush(): bool {
        return true;
    }

    public function stream_lock(): bool {
        return false;
    }

    public function stream_metadata(  string $path , int $option , $value ): bool {
        return false;
    }

    public function rename( string $oldName, $newName ): bool {
        return false;
    }

    public function unlink( string $path ): bool {
        return false;
    }

}
