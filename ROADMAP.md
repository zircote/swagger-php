# Roadmap

## Overview
This is a high level roadmap predominantly for **v6**, **v7** and **v8**.

In particular this is in relation to the introduction of the new _Spec_ attributes and processing pipeline and what may/will happen when.

## Timeline
### v6
`6.5.0` saw the introduction of the new `Spec` system. Right now this is considered not yet production ready.
The remainder of **v6** will be defined by improving and completing the new system.

### v7
This is where things will start to turn:
* Default mode will switch to `hybrid` - this means existing `classic` projects should keep working via the bridge (probably with the exception of `NelmioApiDocBundle`, as that relies on a lot of actual `classic` features), although they will be routed through the new `spec` pipeline via a custom bridge.
* All classic code - annotations + attributes and related pipeline code will be marked `deprecated`
* All code marked `depecated` in **v6** will be removed
* Bridge is marked `deprecated`
* `Builder::setMode()` is marked `deprecated`

## v8
`classic` is removed from the codebase, leaving only the spec pipeline. Annotations are no longer supported at all.
