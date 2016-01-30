# Advanced

## Flow
* Finder crawls the filesystem
* The (Static)Analyser read the files and builds an Analysis object.
* The Analysis object is then processed by the Processors.
* The Analysis/Annotations are validated to notify the user of any known issues.
* The Swagger annotation then contains all annotations and generates the swagger.json 

## Annotation Context
The annotations contain metadata stored in a Context object which has 2 main purposes:

1. It contains the data thats needed by the processors to infer values.   
2. When validation detects an error it can print the location (file and line number) of the offending annotation.   

## Analysis
Contains all detected annotations and other relevant meta data.

It uses a SplObjectStorage to store the annotations, which is like an array but doesn't contain duplicate entries.
