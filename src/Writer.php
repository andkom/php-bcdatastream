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
     * @param int $int
     * @return Writer
     */
    public function writeInt(string $format, int $int): self
    {
        $bytes = pack($format, $int);
        $bytes = ByteOrder::convert($bytes, $this->byteOrder);

        return $this->write($bytes);
    }

    /**
     * @param int $int
     * @return Writer
     */
    public function writeVarInt(int $int): self
    {
        $len = 0;
        $bytes = '';

        while (true) {
            $bytes .= chr(($int & 0x7f) | ($len ? 0x80 : 0x00));
            if ($int <= 0x7f) {
                break;
            }
            $int = ($int >> 7) - 1;
            $len++;
        }

        return $this->write(strrev($bytes));
    }

    /**
     * @param int $int
     * @return Writer
     */
    public function writeCompactSize(int $int): self
    {
        if ($int < 0) {
            throw new \InvalidArgumentException('Size is zero.');
        } elseif ($int < 253) {
            return $this->write(chr($int));
        } elseif ($int < 2 ** 16) {
            return $this->write("\xfd")->writeInt('S', $int);
        } elseif ($int < 2 ** 32) {
            return $this->write("\xfe")->writeInt('L', $int);
        } elseif ($int < 2 ** 64) {
            return $this->write("\xff")->writeInt('Q', $int);
        }
        return $this;
    }

    /**
     * @param string $string
     * @return Writer
     */
    public function writeString(string $string): self
    {
        return $this->writeCompactSize(strlen($string))->write($string);
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