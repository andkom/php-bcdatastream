<?php

declare(strict_types=1);

namespace AndKom\BCDataStream\Tests;

use AndKom\BCDataStream\ByteOrder;
use AndKom\BCDataStream\Reader;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    public function testRead()
    {
        $this->assertEquals((new Reader())->getBuffer(), '');
        $this->assertEquals((new Reader())->getPosition(), 0);
        $this->assertEquals((new Reader())->setBuffer('test')->getBuffer(), 'test');

        $this->assertEquals((new Reader('test'))->setPosition(1)->getPosition(), 1);
        $this->assertEquals((new Reader('test')), 'test');
        $this->assertEquals((new Reader("testtest"))->setPosition(2)->read(4), 'stte');

        $this->assertEquals((new Reader("\x04"))->readVarInt(), 4);
        $this->assertEquals((new Reader("\x04test"))->readString(), 'test');
        $this->assertEquals((new Reader("\x01\x00"))->readInt16(), 1);
        $this->assertEquals((new Reader("\x01\x00"))->readUInt16(), 1);
        $this->assertEquals((new Reader("\x01\x00\x00\x00"))->readInt32(), 1);
        $this->assertEquals((new Reader("\x01\x00\x00\x00"))->readUInt32(), 1);
        $this->assertEquals((new Reader("\x01\x00\x00\x00\x00\x00\x00\x00"))->readInt64(), 1);
        $this->assertEquals((new Reader("\x01\x00\x00\x00\x00\x00\x00\x00"))->readUInt64(), 1);
        $this->assertEquals((new Reader("\x00\x01"))->setByteOrder(ByteOrder::BO_BE)->readInt16(), 1);
        $this->assertEquals((new Reader("\x00\x01"))->setByteOrder(ByteOrder::BO_BE)->readUInt16(), 1);
        $this->assertEquals((new Reader("\x00\x00\x00\x01"))->setByteOrder(ByteOrder::BO_BE)->readInt32(), 1);
        $this->assertEquals((new Reader("\x00\x00\x00\x01"))->setByteOrder(ByteOrder::BO_BE)->readUInt32(), 1);
        $this->assertEquals((new Reader("\x00\x00\x00\x00\x00\x00\x00\x01"))->setByteOrder(ByteOrder::BO_BE)->readInt64(), 1);
        $this->assertEquals((new Reader("\x00\x00\x00\x00\x00\x00\x00\x01"))->setByteOrder(ByteOrder::BO_BE)->readUInt64(), 1);
        $this->assertEquals((new Reader("\0"))->readBoolean(), false);
        $this->assertEquals((new Reader("\1"))->readBoolean(), true);
    }
}