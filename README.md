Networq CLI
===========

Command-line utilities + package manager for your [Networq](https://github.com/networq) projects

## Usage

### Installation

    $ git clone git@github.com:networq/networq-cli.git
    $ cd networq-cli
    $ composer install

### PATH Environment variable

It's suggested to add the `bin/` directory of this project to your global PATH variable.
This way you can easily use the `networq` command from all of your project directories.

If you're using bash, simply add the following line to your `~/.bashrc` file (adjust the path for your specific setup)

    export PATH="~/git/networq/networq-cli/bin/:$PATH"

You'll need to start a new bash session. Either restart your terminal, open a new terminal, or type `bash` in the existing terminal.

You should now be able to type `networq` from any directory and use it's utilities in all of your Networq projects.

### Package information

Use `cd` to go to any package directory, and run `networq package` to list all of the package details.

You'll find the Package's name, description, license and list of dependencies.

### Package dependencies

Use `cd` to go to any package directory, and run `networq install` to install all of your package's dependencies.

Running this command will create a `packages/` directory in your current working directory and
recursively installs all packages in `packages/ownerName/packageName`.

This works similar to npm/yarn/bower/composer/etc package managers.

Note that this simple package manager does not currently supports package version resolving.
It can only install the `latest` version of each of it's dependencies.

It does support recursive package resolving, meaning that if any of your sub-packages has a dependencies of it's own, it will automatically install those packages too.

The packages are installed using git. If the package is not yet installed, it will use `git clone` to create the package directory. If the package is already installed, it will use `git pull` to fetch any updates.

This means that `networq install` will always make sure you have the latest versions of all of your dependencies.

### Package naming + repository naming

Dependencies are listed as `ownerName/packageName`, i.e. `acme/cooking`. The package manager will
then clone this package from `https://github/ownerName/packageName-package`. In this case `https://github.com/acme/cooking-package`.

### Development / testing

This package uses the [Networq PHP library](https://github.com/networq/networq-php) to do most of the heavy lifting.

During development and testing of Networq CLI you can use [autotune](https://github.com/linkorb/autotune) to easily work on both projects at the same time.

## License

MIT. Please refer to the [license file](LICENSE) for details.

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
