# SiteMaker Content Tags

phpBB Sitemaker Content Tags is an Extension for [phpBB 3.2](https://www.phpbb.com/)

[![Travis branch](https://img.shields.io/travis/blitze/phpBB-ext-sitemaker_content_tags/master.svg?style=flat)](https://travis-ci.org/blitze/phpBB-ext-sitemaker_content_tags) [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/blitze/phpBB-ext-sitemaker_content_tags/master.svg?style=flat)](https://scrutinizer-ci.com/g/blitze/phpBB-ext-sitemaker_content_tags/?branch=master)

## Description

Add tags to your phpBB Sitemaker Content topics

## Features

* Choose how you want to display tags - list, buttons, label
* Display related content based on tags
* Tag cloud block

## Minimum Requirements

* [phpBB 3.2](https://www.phpbb.com/)
* [phpBB Sitemaker 3.1.0](https://github.com/blitze/phpBB-ext-sitemaker)
* [phpBB Sitemaker Content 3.0.0-dev](https://github.com/blitze/phpBB-ext-sitemaker_content)

## Installation

Copy the extension to phpBB/ext/blitze/tags

Go to "ACP" > "Customise" > "Extensions" and enable the "SiteMaker Content Tags" extension.

## Tests and Continuous Integration

We use Travis-CI as a continuous integration server and phpunit for our unit testing. See more information on the [phpBB Developer Docs](https://area51.phpbb.com/docs/dev/31x/testing/index.html).
To run the tests locally, you need to install phpBB from its Git repository. Afterwards run the following command from the phpBB Git repository's root:

Windows:

    phpBB\vendor\bin\phpunit.bat -c phpBB\ext\blitze\tags\phpunit.xml.dist

others:

    phpBB/vendor/bin/phpunit -c phpBB/ext/blitze/tags/phpunit.xml.dist

## License

[GPLv2](license.txt)
