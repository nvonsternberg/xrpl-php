<?php declare(strict_types=1);

namespace XRPL_PHP\Core\RippleBinaryCodec\Types;

use BI\BigInteger;
use XRPL_PHP\Core\Buffer;
use XRPL_PHP\Core\RippleBinaryCodec\Serdes\BinaryParser;

class UnsignedInt32 extends  UnsignedInt
{
    public function __construct(?int $value = null)
    {
        if ($value === null) {
            new BigInteger();
        } else {
            $this->value = new BigInteger((string)$value);
        }
    }

    public function fromParser(BinaryParser $parser, ?int $lengthHint = null): SerializedType
    {
        $fromParser = $parser->readUInt32();
        return new UnsignedInt32($fromParser);
    }

    public function fromValue(SerializedType $value, ?int $number): SerializedType
    {
        // TODO: Implement fromValue() method.
    }

    public function toBytes(): Buffer
    {
        $hexStr = $this->value->toHex();
        $uint32HexStr = str_pad($hexStr, 8, "0", STR_PAD_LEFT);

        return Buffer::from($uint32HexStr, 'hex');
    }

    public function toHex(): string
    {
        return strtoupper($this->toBytes()->toString());
    }
}