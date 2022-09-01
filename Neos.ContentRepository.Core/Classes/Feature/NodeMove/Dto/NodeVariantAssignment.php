<?php

/*
 * This file is part of the Neos.ContentRepository package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

declare(strict_types=1);

namespace Neos\ContentRepository\Core\Feature\NodeMove\Dto;

use Neos\ContentRepository\Core\DimensionSpace\OriginDimensionSpacePoint;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;

/**
 * A node variant assignment, identifying a node variant by node aggregate id and origin dimension space point.
 *
 * This is used in structural operations like node move to assign a new node within the same content stream
 * as a new parent, sibling etc.
 *
 * In case of move, this is the "target node" underneath which or next to which we want to move our source.
 *
 * @api DTO of {@see NodeAggregateWasMoved} event
 */
final class NodeVariantAssignment implements \JsonSerializable
{
    public function __construct(
        public readonly NodeAggregateId $nodeAggregateId,
        public readonly OriginDimensionSpacePoint $originDimensionSpacePoint
    ) {
    }

    /**
     * @param array<string,mixed> $array
     */
    public static function createFromArray(array $array): self
    {
        return new self(
            NodeAggregateId::fromString($array['nodeAggregateId']),
            OriginDimensionSpacePoint::fromArray($array['originDimensionSpacePoint'])
        );
    }

    /**
     * @return array<string,\JsonSerializable>
     */
    public function jsonSerialize(): array
    {
        return [
            'nodeAggregateId' => $this->nodeAggregateId,
            'originDimensionSpacePoint' => $this->originDimensionSpacePoint,
        ];
    }

    public function __toString(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }
}
