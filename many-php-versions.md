# How to install many PHP versions?

## Commands

```
PHP_VERSION=53
brew install homebrew/php/php$PHP_VERSION
brew unlink homebrew/php/php$PHP_VERSION
PHP_PATH=$(find /usr/local/Cellar/php$PHP_VERSION -name php | grep bin/php)
sudo ln -fns $PHP_PATH /usr/local/bin/php$PHP_VERSION
```

## Requirements

* [Homebrew](http://brew.sh/)