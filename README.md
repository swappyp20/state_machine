Prototype for a Commerce 2.x workflow solution: https://www.drupal.org/node/2626988

The current name is temporary, the module is not tied to Commerce in any way
and should live in its own namespace.

High level overview
-------------------
A workflow is a set of states and transitions that an entity goes through during its lifecycle.
A transition represents a one-way link between two states and has its own label.

An entity can have multiple workflows, each in its own state field.
An order might have checkout, payment, fulfilment workflows.
A node or product might have legal and marketing workflows.
Workflow groups are used to group workflows used for the same purpose (e.g. payment workflows).

The state field provides an API for getting the allowed transitions/states, used
by validation and widgets/formatters.

Architecture
------------
Workflow and WorkflowGroup are plugins defined in YAML, similar to menu links.
This leaves room for a future entity-based UI.

The current state is stored in a StateItem field.
A field setting specifies the used workflow, or a value callback that allows
the workflow to be resolved at runtime (checkout workflow based on the used plugin, etc.

Credits
-------
Initial code by Pedro Cambra.
