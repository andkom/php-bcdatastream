<?php

declare(strict_types=1);

namespace AndKom\BCDataStream;

/**
 * Class Writer
 * @package AndKom\BCDataStream
 */
class Writer
{
    /**
     * @var string
     */
    protected $buffer;

    /**
     * @var int
     */
    protected $byteOrder;

    /**
     * Writer constructor.
     * @param int $byteOrder
     */
    public function __construct(int $byteOrder = ByteOrder::BO_LE)
    {
        $this->setByteOrder($byteOrder);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getBuffer();
    }

    /**
     * @return Writer
     */
    public function clear(): self
    {
        $this->buffer = '';

        return $this;
    }

    /**
     * @return string
     */
    public function getBuffer(): string
    {
        return $this->buffer;
    }

    /**
     * @return int
     */
    public function getByteOrder(): int
    {
        return $this->byteOrder;
    }

    /**
     * @param int $byteOrder
     * @return Writer
     */
    public function setByteOrder(int $byteOrder): self
    {
        $this->byteOrder = $byteOrder;

        return $this;
    }

    /**
     * @param string $bytes
     * @return Writer
     */
    public function write(string $bytes): self
    {
        $this->buffer .= $bytes;

        return $this;
    }

    /**
     * @param string $format
     * @param int $num
     * @return Writer
     */
    public function writeInt(string $format, int $num): self
    {
        $bytes = pack($format, $num);
        $bytes = ByteOrder::convert($bytes, $this->byteOrder);

        return $this->write($bytes);
    }

    /**
     * @param int $size
     * @return Writer
     */
    public function writeVarInt(int $size): self
    {
        if ($size < 0) {
            throw new \InvalidArgumentException('Size is zero.');
        } elseif ($size < 253) {
            return $this->write(chr($size));
        } elseif ($size < 2 ** 16) {
            return $this->write("\xfd")->writeInt('S', $size);
        } elseif ($size < 2 ** 32) {
            return $this->write("\xfe")->writeInt('L', $size);
        } elseif ($size < 2 ** 64) {
            return $this->write("\xff")->writeInt('Q', $size);
        }
        return $this;
    }

    /**
     * @param string $string
     * @return Writer
     */
    public function writeString(string $string): self
    {
        return $this->writeVarInt(strlen($string))->write($string);
    }

    /**
     * @param int $int
     * @return Writer
     */
    public function writeInt16(int $int): self
    {
        return $this->writeInt('s', $int);
    }

    /**
     * @param int $int
     * @return Writer
     */
    public function writeUInt16(int $int): self
    {
        return $this->writeInt('S', $int);
    }

    /**
     * @param int $int
     * @return Writer
     */
    public function writeInt32(int $int): self
    {
        return $this->writeInt('l', $int);
    }

    /**
     * @param int $int
     * @return Writer
     */
    public function writeUInt32(int $int): self
    {
        return $this->writeInt('L', $int);
    }

    /**
     * @param int $int
     * @return Writer
     */
    public function writeInt64(int $int): self
    {
        return $this->writeInt('q', $int);
    }

    /**
     * @param int $int
     * @return Writer
     */
    public function writeUInt64(int $int): self
    {
        return $this->writeInt('Q', $int);
    }

    /**
     * @param bool $boolean
     * @return Writer
     */
    public function writeBoolean(bool $boolean): self
    {
        return $this->write($boolean ? "\1" : "\0");
    }
}