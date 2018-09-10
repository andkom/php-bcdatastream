<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinWallet\Tests;

use AndKom\BCDataStream\ByteOrder;
use AndKom\BCDataStream\Writer;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    public function testWrite()
    {
        $this->assertEquals((new Writer())->writeVarInt(0x00)->getBuffer(), "\x00");
        $this->assertEquals((new Writer())->writeVarInt(0x7f)->getBuffer(), "\x7f");
        $this->assertEquals((new Writer())->writeVarInt(0x80)->getBuffer(), "\x80\x00");
        $this->assertEquals((new Writer())->writeVarInt(0x1234)->getBuffer(), "\xa3\x34");
        $this->assertEquals((new Writer())->writeVarInt(0xffff)->getBuffer(), "\x82\xfe\x7f");
        $this->assertEquals((new Writer())->writeVarInt(0x123456)->getBuffer(), "\xc7\xe7\x56");
        $this->assertEquals((new Writer())->writeVarInt(0x80123456)->getBuffer(), "\x86\xff\xc7\xe7\x56");
        $this->assertEquals((new Writer())->writeVarInt(0xffffffff)->getBuffer(), "\x8e\xfe\xfe\xfe\x7f");
        $this->assertEquals((new Writer())->writeVarInt(0x7fffffffffffffff)->getBuffer(), "\xfe\xfe\xfe\xfe\xfe\xfe\xfe\xfe\x7f");

        $this->assertEquals((new Writer())->writeCompactSize(4)->getBuffer(), "\x04");

        $this->assertEquals((new Writer())->writeString('test')->getBuffer(), "\x04test");

        $this->assertEquals((new Writer())->writeInt16(1)->getBuffer(), "\x01\x00");
        $this->assertEquals((new Writer())->writeUInt16(1)->getBuffer(), "\x01\x00");
        $this->assertEquals((new Writer())->writeInt32(1)->getBuffer(), "\x01\x00\x00\x00");
        $this->assertEquals((new Writer())->writeUInt32(1)->getBuffer(), "\x01\x00\x00\x00");
        $this->assertEquals((new Writer())->writeInt64(1)->getBuffer(), "\x01\x00\x00\x00\x00\x00\x00\x00");
        $this->assertEquals((new Writer())->writeUInt64(1)->getBuffer(), "\x01\x00\x00\x00\x00\x00\x00\x00");

        $this->assertEquals((new Writer())->setByteOrder(ByteOrder::BO_BE)->writeInt16(1)->getBuffer(), "\x00\x01");
        $this->assertEquals((new Writer())->setByteOrder(ByteOrder::BO_BE)->writeUInt16(1)->getBuffer(), "\x00\x01");
        $this->assertEquals((new Writer())->setByteOrder(ByteOrder::BO_BE)->writeInt32(1)->getBuffer(), "\x00\x00\x00\x01");
        $this->assertEquals((new Writer())->setByteOrder(ByteOrder::BO_BE)->writeUInt32(1)->getBuffer(), "\x00\x00\x00\x01");
        $this->assertEquals((new Writer())->setByteOrder(ByteOrder::BO_BE)->writeInt64(1)->getBuffer(), "\x00\x00\x00\x00\x00\x00\x00\x01");
        $this->assertEquals((new Writer())->setByteOrder(ByteOrder::BO_BE)->writeUInt64(1)->getBuffer(), "\x00\x00\x00\x00\x00\x00\x00\x01");

        $this->assertEquals((new Writer())->writeBoolean(false)->getBuffer(), "\0");
        $this->assertEquals((new Writer())->writeBoolean(true)->getBuffer(), "\1");
    }
}