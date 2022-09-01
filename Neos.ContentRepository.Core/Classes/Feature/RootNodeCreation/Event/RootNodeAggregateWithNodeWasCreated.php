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

namespace Neos\ContentRepository\Core\Feature\RootNodeCreation\Event;

use Neos\ContentRepository\Core\DimensionSpace\DimensionSpacePointSet;
use Neos\ContentRepository\Core\SharedModel\Workspace\ContentStreamId;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\Feature\Common\EmbedsContentStreamAndNodeAggregateId;
use Neos\ContentRepository\Core\Feature\Common\PublishableToOtherContentStreamsInterface;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateClassification;
use Neos\ContentRepository\Core\SharedModel\User\UserId;
use Neos\ContentRepository\Core\EventStore\EventInterface;

/**
 * A root node aggregate and its initial node were created
 *
 * @api events are the persistence-API of the content repository
 */
final class RootNodeAggregateWithNodeWasCreated implements
    EventInterface,
    PublishableToOtherContentStreamsInterface,
    EmbedsContentStreamAndNodeAggregateId
{
    public function __construct(
        public readonly ContentStreamId $contentStreamId,
        public readonly NodeAggregateId $nodeAggregateId,
        public readonly NodeTypeName $nodeTypeName,
        /** Root nodes by definition cover *all* dimension space points; so we need to include the full list here. */
        public readonly DimensionSpacePointSet $coveredDimensionSpacePoints,
        public readonly NodeAggregateClassification $nodeAggregateClassification,
        public readonly UserId $initiatingUserId
    ) {
    }

    public function getContentStreamId(): ContentStreamId
    {
        return $this->contentStreamId;
    }

    public function getNodeAggregateId(): NodeAggregateId
    {
        return $this->nodeAggregateId;
    }

    public function createCopyForContentStream(ContentStreamId $targetContentStreamId): self
    {
        return new self(
            $targetContentStreamId,
            $this->nodeAggregateId,
            $this->nodeTypeName,
            $this->coveredDimensionSpacePoints,
            $this->nodeAggregateClassification,
            $this->initiatingUserId
        );
    }

    public static function fromArray(array $values): self
    {
        return new self(
            ContentStreamId::fromString($values['contentStreamId']),
            NodeAggregateId::fromString($values['nodeAggregateId']),
            NodeTypeName::fromString($values['nodeTypeName']),
            DimensionSpacePointSet::fromArray($values['coveredDimensionSpacePoints']),
            NodeAggregateClassification::from($values['nodeAggregateClassification']),
            UserId::fromString($values['initiatingUserId']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'contentStreamId' => $this->contentStreamId,
            'nodeAggregateId' => $this->nodeAggregateId,
            'nodeTypeName' => $this->nodeTypeName,
            'coveredDimensionSpacePoints' => $this->coveredDimensionSpacePoints,
            'nodeAggregateClassification' => $this->nodeAggregateClassification,
            'initiatingUserId' => $this->initiatingUserId,
        ];
    }
}
