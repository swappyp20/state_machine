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

Example yourmodule.workflow_groups.yml:
```yaml
order:
  label: Order
  entity_type: commerce_order
```
Example yourmodule.workflows.yml:
```yaml
default:
  id: default
  label: Default
  group: order
  states:
    new:
      label: New
    fulfillment:
      label: Fulfilment
    completed:
      label: Completed
    canceled:
      label: Canceled
  transitions:
    create:
      label: Create
      from: [new]
      to:   fulfillment
    fulfill:
      label: Fulfill
      from: [fulfillment]
      to: completed
    cancel:
      label: Cancel
      from: [new, validation, fulfillment]
      to:   canceled
```

Transitions can be further restricted by [guards](https://github.com/bojanz/commerce_workflow/blob/8.x-1.x/src/WorkflowGuard/WorkflowGuardInterface.php), which are implemented as tagged services:
```yaml
  mymodule.fulfillment_guard:
    class: Drupal\mymodule\WorkflowGuard\FulfillmentGuard
    tags:
      - { name: commerce_workflow.workflow_guard, group: order }
```
The group argument allows the guard factory to only instantiate the guards relevant
to a specific workflow group.

The current state is stored in a [StateItem](https://github.com/bojanz/commerce_workflow/blob/8.x-1.x/src/Plugin/Field/FieldType/StateItem.php) field.
A field setting specifies the used workflow, or a value callback that allows
the workflow to be resolved at runtime (checkout workflow based on the used plugin, etc.

A validator is provided that ensures that the specified state is valid (exists in the
workflow and is in the allowed transitions).

Currently uses only the options_select widget.
A dropbutton node-like widget could be provided, but was initially omitted becase
it only makes sense for the main entity workflow, which nodes already have (published status),
while Commerce is pursuing a different UX.

A formatter will be provided that outputs a form with the allowed transitions,
allowing workflow changes to happen outside of the edit form.

Credits
-------
Initial code by Pedro Cambra.

Inspired by https://github.com/winzou/state-machine
