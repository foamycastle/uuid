<?php

namespace Foamycastle\UUID\Builder;

use Foamycastle\UUID\Field;
use Foamycastle\UUID\Field\FieldKey;
use Foamycastle\UUID\Provider\ProviderKey;
use Foamycastle\UUID\UUIDBuilder;
use Foamycastle\UUID\Batchable;

/**
 * UUID Type 1: Gregorian Time-based.
 * @link https://datatracker.ietf.org/doc/html/rfc9562#name-uuid-version-1
 * @author Aaron Sollman<unclepong@gmail.com>
 *
 */
class UUIDVersion1 extends UUIDBuilder implements Batchable
{
    protected function __construct(?string $staticNode=null)
    {
        parent::__construct(1);

        /*
         * If a primitive string was supplied as a static node,
         * wrap it in a StaticNodeProvider class. Otherwise, use a
         * SystemNodeProvider
         */

        $nodeProvider = is_string($staticNode)
            ? [ProviderKey::StaticNode,$staticNode]
            : [ProviderKey::SystemNode];


        //Provides the integer value of time
        $this->registerProvider(ProviderKey::GregorianTime);

        //Provides the clock sequence
        $this->registerProvider(ProviderKey::Counter,0,0x3fff);

        //Provide the node portion of the UUID
        $this->registerProvider(...$nodeProvider);

        //Time low field
        $this->registerField(
            FieldKey::TIME_LO,
            ProviderKey::GregorianTime
        )
            ->length(8)
            ->bitMask(0xFFFFFFFF);

        //Time mid field
        $this->registerField(
            FieldKey::TIME_MID,
            ProviderKey::GregorianTime
        )
            ->length(4)
            ->shiftRight(32)
            ->bitMask(0xFFFF);

        //Time high and version field
        $this->registerField(
            FieldKey::TIME_HIAV,
            ProviderKey::GregorianTime
        )
            ->length(4)
            ->shiftRight(48)
            ->applyVersion(1);

        //Counter and Variant field
        $this->registerField(
            FieldKey::TIME_VAR,
            ProviderKey::Counter
        )
            ->length(4)
            ->applyVariant();

        //Node field
        $this->registerField(
            FieldKey::TIME_NODE,
            $nodeProvider[0]
        )
            ->length(12);
    }

    function batch(int $count): iterable
    {
        return [];
    }

}