# Developer Tools

Command line interface for programmers developing on CradlePHP

## Install

If you already installed Cradle, you may not need to install this because it
should be already included.

```
$ composer require cradlephp/cradle-developer
$ bin/cradle cradlephp/cradle-developer install
```

 - `$ cradle connect app-1` - Connects to a server. see: `config/deploy.php`
 - `$ cradle help` - General help menu
 - `$ cradle install` - Installs Cradle
 - `$ cradle install -f | --force` - Installs Cradle force overriding files
 - `$ cradle install --skip-sql` - Installs Cradle, but skips the sql part
 - `$ cradle install --skip-versioning` - Installs Cradle but skips updating the packages
 - `$ cradle install -h 127.0.0.1 -u root -p 123` - Installs Cradle with the given database information
 - `$ cradle server -h 127.0.0.1 -p 8888` - Starts a PHP server
 - `$ cradle update` - Updates all packages to their latest version
 - `$ cradle deploy` - Deploy Commands
 - `$ cradle deploy help` - Deploy help menu
 - `$ cradle deploy production` - Deploys code to server see: `config/deploy.php`
 - `$ cradle deploy s3` - Uploads static assets to S3. see: `config/services.php`
 - `$ cradle deploy s3 --include-yarn` - Uploads static assets to S3 including Yarn folder
 - `$ cradle deploy s3 --include-upload` - Uploads static assets to S3 including upload folder
 - `$ cradle elastic` - ElasticSearch Commands
 - `$ cradle elastic help` - ElasticSearch help menu
 - `$ cradle elastic flush` - Truncates the entire index
 - `$ cradle elastic flush foo/bar` - Truncates the indexes related to given package
 - `$ cradle elastic map` - Submits the ElasticSearch schema
 - `$ cradle elastic map foo/bar` - Submits the ElasticSearch schema related to given package
 - `$ cradle elastic populate` - Populates all the indexes of every package
 - `$ cradle elastic populate foo/bar` - Populates the indexes given the package
 - `$ cradle package` - Package commands
 - `$ cradle package help` - Package help menu
 - `$ cradle package install foo/bar` - Installs a package from packagist
 - `$ cradle package install foo/bar 1.0.0` - Installs a specific version from packagist
 - `$ cradle package list` - Lists out all the available packages
 - `$ cradle package remove foo/bar` - Removes a package
 - `$ cradle package search foobar` - Searches packagist for a particular package
 - `$ cradle package update foo/bar` - Updates a package to its latest version
 - `$ cradle package update foo/bar 1.0.0` - Updates package to a particular version
 - `$ cradle redis` - Redis Commands
 - `$ cradle redis help` - Redis help menu
 - `$ cradle redis flush` - Truncates the entire cache
 - `$ cradle redis flush foo/bar` - Truncates cache related to given package
 - `$ cradle sql` - SQL Commands
 - `$ cradle sql help` - SQL help menu
 - `$ cradle sql build` - Rebuilds the database schema
 - `$ cradle sql build foo/bar` - Rebuilds the database tables related to given package
 - `$ cradle sql flush` - Truncates the entire database
 - `$ cradle sql flush foo/bar` - Truncates database tables related to given package
 - `$ cradle sql populate` - Populates all the tables of every package
 - `$ cradle sql populate foo/bar` - Populates the tables given the package

 ----

 <a name="contributing"></a>
 # Contributing to Cradle PHP

 Thank you for considering to contribute to Cradle PHP.

 Please DO NOT create issues in this repository. The official issue tracker is located @ https://github.com/CradlePHP/cradle/issues . Any issues created here will *most likely* be ignored.

 Please be aware that master branch contains all edge releases of the current version. Please check the version you are working with and find the corresponding branch. For example `v1.1.1` can be in the `1.1` branch.

 Bug fixes will be reviewed as soon as possible. Minor features will also be considered, but give me time to review it and get back to you. Major features will **only** be considered on the `master` branch.

 1. Fork the Repository.
 2. Fire up your local terminal and switch to the version you would like to
 contribute to.
 3. Make your changes.
 4. Always make sure to sign-off (-s) on all commits made (git commit -s -m "Commit message")

 ## Making pull requests

 1. Please ensure to run [phpunit](https://phpunit.de/) and
 [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) before making a pull request.
 2. Push your code to your remote forked version.
 3. Go back to your forked version on GitHub and submit a pull request.
 4. All pull requests will be passed to [Travis CI](https://travis-ci.org/CradlePHP/cradle-developer) to be tested. Also note that [Coveralls](https://coveralls.io/github/CradlePHP/cradle-developer) is also used to analyze the coverage of your contribution.
