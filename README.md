# Consumer Provider README.md

This is a useful package for copying data to and from 2 disparate data stores.  It provides the connecting code, allowing the clients of this package to focus on the functionality of the consumer and provider parts.  Some examples are copying data from one type of database to another database type, scraping web data into another data source.

## Installation:
composer require digitalzenworks/consumer-provider

## Usage:
vendor/digitalzenworks/consumer-provider/SourceCode/process &lt;Consumer Class Name&gt; &lt;Provider Class Name&gt;

## Example:
There is an example in the Example directory.
