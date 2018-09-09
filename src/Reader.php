<?php

declare(strict_types=1);

namespace AndKom\BCDataStream;

/**
 * Class Reader
 * @package AndKom\BCDataStream
 */
class Reader
{
    /**
     * @var string
     */
    protected $buffer;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var int
     */
    protected $byteOrder;

    /**
     * Reader constructor.
     * @param string $buffer
     * @param int $position
     * @param int $byteOrder
     */
    public function __construct(string $buffer = '', int $position = 0, int $byteOrder = ByteOrder::BO_LE)
    {
        $this->setBuffer($buffer);
        $this->setPosition($position);
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
     * @return string
     */
    public function getBuffer(): string
    {
        return $this->buffer;
    }

    /**
     * @param string $buffer
     * @return Reader
     */
    public function setBuffer(string $buffer): self
    {
        $this->buffer = $buffer;
        $this->position = 0;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return Reader
     */
    public function setPosition(int $position): self
    {
        if ($this->position < 0) {
            throw new \InvalidArgumentException('Negative position.');
        }

        if ($this->position > strlen($this->buffer)) {
            throw new \InvalidArgumentException('Position is greater then buffer length.');
        }

        $this->position = $position;

        return $this;
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
     * @return Reader
     */
    public function setByteOrder(int $byteOrder): self
    {
        $this->byteOrder = $byteOrder;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return strlen($this->buffer);
    }

    /**
     * @return bool
     */
    public function isEOF(): bool
    {
        return $this->position >= strlen($this->buffer);
    }

    /**
     * @param int $size
     * @return string
     */
    public function read(int $size): string
    {
        if ($this->position + $size > strlen($this->buffer)) {
            throw new \InvalidArgumentException("Not enough buffer to read $size byte(s).");
        }

        $bytes = substr($this->buffer, $this->position, $size);

        $this->position += strlen($bytes);

        return $bytes;
    }

    /**
     * @param string $format
     * @return int
     */
    public function readInt(string $format): int
    {
        $size = strlen(pack($format, 1));
        $bytes = $this->read($size);
        $bytes = ByteOrder::convert($bytes, $this->byteOrder);

        list(, $num) = unpack($format, $bytes);

        return $num;
    }

    /**
     * @return int
     */
    public function readVarInt(): int
    {
        $size = ord($this->read(1));

        if ($size == 253) {
            $size = $this->readInt('S');
        } elseif ($size == 254) {
            $size = $this->readInt('L');
        } elseif ($size == 255) {
            $size = $this->readInt('Q');
        }

        return $size;
    }

    /**
     * @return string
     */
    public function readString(): string
    {
        return $this->read($this->readVarInt());
    }

    /**
     * @return int
     */
    public function readInt16(): int
    {
        return $this->readInt('s');
    }

    /**
     * @return int
     */
    public function readUInt16(): int
    {
        return $this->readInt('S');
    }

    /**
     * @return int
     */
    public function readInt32(): int
    {
        return $this->readInt('l');
    }

    /**
     * @return int
     */
    public function readUInt32(): int
    {
        return $this->readInt('L');
    }

    /**
     * @return int
     */
    public function readInt64(): int
    {
        return $this->readInt('q');
    }

    /**
     * @return int
     */
    public function readUInt64(): int
    {
        return $this->readInt('Q');
    }

    /**
     * @return bool
     */
    public function readBoolean(): bool
    {
        return $this->read(1) != chr(0);
    }
}