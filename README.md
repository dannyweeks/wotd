WOTD.fyi
========
This readme is woefully inadequate right now. Sorry about that.

[![StyleCI](https://styleci.io/repos/98942584/shield?branch=master)](https://styleci.io/repos/98942584)

## Requirements

* PHP >= 7.1
* `mysql` PHP extension
* `curl` PHP extension
* `sqlite` PHP Extension (dev)

The composer install step will automatically check for these dependencies.

## Setup

### Install Dependencies

```
composer install
```

### Load Fixtures

```
bin/console doctrine:fixtures:load
```

### Run The Server (Dev)

```
bin/console server:run
```

### Automation!

#### Dev Mode

```
./setup.sh -d
```
#### Test Mode

```
./setup.sh -t
```

#### Production Mode

```
./setup.sh -p
```

#### Help Mode

```
./setup.sh -h
```