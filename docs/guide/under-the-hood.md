# Under the hood

## Processing flow

- The `Generator` iterates over the given sources (Symfony `Finder`, file/directoy list, etc)
- The configured analyser (`AnalyserInterface`) reads the files and builds an `Analysis` object.
  Default (as of v4) is the `ReflectionAnalyser`. Alternatively, there is the `TokenAnalyser` which was the default in v3.
- The `Analysis` object and its annotations are then processed by the configured processors.
- If enabled, the analysis/annotations are validated.
- The root `OpenApi` annotation then contains all annotations and is serialized into YAML/JSON.

## `Context`

Each annotation is associated with a unique `Context` instance. This contains details, collected by the parser/analyser,
about the PHP context where the annotation was found.

Typically, there will be a processor that uses the data to augment/enrich the annotation.

**Examples of the data collected:** 
  - class/interface/trait/enum names
  - property names
  - doctype or native type hints
  - file name and line number

## Analysis

Contains all detected annotations and other relevant meta data.

It uses a `SplObjectStorage` instance to store the parsed annotations.

## Documentation

This documentation is generated with [VitePress](https://vitepress.vuejs.org/)

### Installation
```shell
cd docs
npm install vitepress git-directory-deploy
```

### Workflow

* Edit `.md` files in the `docs` folder
* Update `master`
* `cd docs && npm run deploy`

The last step requires commit rights on `zircote/swagger-php`.
