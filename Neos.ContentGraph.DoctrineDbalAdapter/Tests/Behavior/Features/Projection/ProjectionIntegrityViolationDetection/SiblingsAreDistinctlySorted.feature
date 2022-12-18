@contentrepository
Feature: Run integrity violation detection regarding sibling sorting

  As a user of the CR I want to know whether there are siblings with ambiguous sorting

  Background:
    Given I have the following content dimensions:
      | Identifier | Values      | Generalizations |
      | language   | de, gsw, fr | gsw->de         |
    And I have the following NodeTypes configuration:
    """
    'Neos.ContentRepository:Root': []
    'Neos.ContentRepository.Testing:Document': []
    """
    And the command CreateRootWorkspace is executed with payload:
      | Key                  | Value                |
      | workspaceName        | "live"               |
      | workspaceTitle       | "Live"               |
      | workspaceDescription | "The live workspace" |
      | newContentStreamId   | "cs-identifier"      |
    And the graph projection is fully up to date
    And the event RootNodeAggregateWithNodeWasCreated was published with payload:
      | Key                         | Value                                                    |
      | contentStreamId             | "cs-identifier"                                          |
      | nodeAggregateId             | "lady-eleonode-rootford"                                 |
      | nodeTypeName                | "Neos.ContentRepository:Root"                            |
      | coveredDimensionSpacePoints | [{"language":"de"},{"language":"gsw"},{"language":"fr"}] |
      | nodeAggregateClassification | "root"                                                   |
    And the graph projection is fully up to date

  Scenario: Create two siblings and set the sorting to the same value
    When the event NodeAggregateWithNodeWasCreated was published with payload:
      | Key                         | Value                                                    |
      | contentStreamId             | "cs-identifier"                                          |
      | nodeAggregateId             | "nody-mc-nodeface"                                       |
      | nodeTypeName                | "Neos.ContentRepository.Testing:Document"                |
      | originDimensionSpacePoint   | {"language":"de"}                                        |
      | coveredDimensionSpacePoints | [{"language":"de"},{"language":"gsw"},{"language":"fr"}] |
      | parentNodeAggregateId       | "lady-eleonode-rootford"                                 |
      | nodeAggregateClassification | "regular"                                                |
    And the event NodeAggregateWithNodeWasCreated was published with payload:
      | Key                         | Value                                                    |
      | contentStreamId             | "cs-identifier"                                          |
      | nodeAggregateId             | "noderella-mc-nodeface"                                  |
      | nodeTypeName                | "Neos.ContentRepository.Testing:Document"                |
      | originDimensionSpacePoint   | {"language":"de"}                                        |
      | coveredDimensionSpacePoints | [{"language":"de"},{"language":"gsw"},{"language":"fr"}] |
      | parentNodeAggregateId       | "lady-eleonode-rootford"                                 |
      | nodeAggregateClassification | "regular"                                                |
    And the graph projection is fully up to date
    And I set the following position:
      | Key                  | Value              |
      | contentStreamId      | "cs-identifier"    |
      | dimensionSpacePoint  | {"language":"de"}  |
      | childNodeAggregateId | "nody-mc-nodeface" |
      | newPosition          | 128                |
    And I set the following position:
      | Key                  | Value                   |
      | contentStreamId      | "cs-identifier"         |
      | dimensionSpacePoint  | {"language":"de"}       |
      | childNodeAggregateId | "noderella-mc-nodeface" |
      | newPosition          | 128                     |
    And I run integrity violation detection
    Then I expect the integrity violation detection result to contain exactly 1 error
    And I expect integrity violation detection result error number 1 to have code 1597910918