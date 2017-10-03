#!/usr/bin/env bash

initialChecks() {
    errors=false;

    # Gotta have php
    php=$(which php);

    if [ ! "${php}" ]; then
        # If php isn't available, we can't continue.
        printf "PHP needs to be installed and in your path to use this script.\n"

        error=true
    fi

    # Gotta have git installed and in your path.
    git=$(which git)

    if [ ! "${git}" ]; then
        # If git isn't available, we can't continue.
        printf "Git needs to be installed and in your path to use this script.\n"

        errors=true
    fi

    # If there were any errors, return false. Otherwise, return true
    # Doing it this way will allow us to add more checks later on.
    if [ "${errors}" = "true" ]; then
        return 1
    else
        return 0
    fi
}

getComposer() {
    # Check for a composer.phar file in the current directory.
    if [ -f "composer.phar" ]; then
        composer="composer.phar"

        return
    fi

    # Check to see if composer is installed globally and in the path.
    composer=$(which composer)

    if [ "${composer}" ]; then
        return
    fi

    # We couldn't find it, let's download it.
    printf "Couldn't find composer anywhere, attempting to download it..\n"

    expectedSignature=$("${php}" -r "readfile('https://composer.github.io/installer.sig');")

    "${php}" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

    actualSigature=$("${php}" -r "echo hash_file('SHA384', 'composer-setup.php');")

    if [ "${expectedSignature}" != "${actualSigature}" ]; then
        >&2 printf 'ERROR: Invalid installer signature!'

        rm composer-setup.php

        exit 1
    fi

    if ! "${php}" composer-setup.php --quiet; then
        printf "An error occurred while downloading composer.\n"

        exit 1
    fi

    composer="composer.phar"

    rm composer-setup.phar
}

fixPermissions() {
    printf "Fixing permissions on cache and log directories...\n"

    chmod a+rwx var/cache/
    chmod a+rwx var/logs/
}

runMigrations() {
    printf "Checking for available migrations...\n"

    # Here we are calling Doctrine to see if there are any migrations available.
    # The `awk` part of the command is simply searching for "New Migrations" and
    # then printing the corresponding field.
    # The `sed` portion of the command simply strips the formatting codes.
    migrations=$(
        "${php}" bin/console doctrine:migrations:status \
        | awk '/New Migrations:/ {print $NF}' \
        | sed -e "s/\x1B\[([0-9]{1,2}(;[0-9]{1,2})?)?[m|K]//g"
    )

    if [ "${migrations}" -gt "0" ]; then
        "${php}" bin/console doctrine:migrations:migrate
    fi
}

clearCache() {
    printf "Clearing the cache...\n"

    if [ ! -z "${1}" ] && [ "${1}" = "hardcore" ]; then
        rm -rf var/cache/*

        return
    fi

    "${php}" bin/console cache:clear
}

trapUser() {
    # Handle user interrupts.
    printf "\n\nTerminated by user!\n\n"

    exit 1
}

trapGeneral() {
    # Handle other interrupts.
    printf "\n\nUnexpected interruption!\n\n"

    exit 1
}

trap trapUser INT QUIT

trap trapGeneral HUP TERM

helpText() {
    printf "Usage: %s [OPTION]\n\
    \n\
    Options:\n\
    \t-h, --help\t\tDisplays this help text.\n\
    \t-d, --dev\t\tSetup the project in \"dev\" mode.\n\
    \t-t, --test\t\tSetup the project in \"test\" mode.\n\
    \t-p, --prod\t\tSetup the project in \"prod\" mode (excludes dev dependencies).\n" "${0}"

    exit
}

# Parse Option
case "${1}" in
    -h | --help)
        helpText
    ;;
    -d | --dev)
        if initialChecks; then
            printf "Setting up in dev mode...\n"

            # Find composer or download it
            getComposer

            # Setup Symfony and dependencies
            export SYMFONY_ENV=dev
            "${php}" "${composer}" install --optimize-autoloader --prefer-dist

            # Correct permissions on cache and log directories
            fixPermissions

            # Check for and run migrations
            runMigrations

            # clear the cache (hardcore mode)
            clearCache hardcore
        fi
    ;;
    -t | --test)
        if initialChecks; then
            printf "Setting up in test mode...\n"

            # Find composer or download it
            getComposer

            # Setup Symfony and dependencies.
            export SYMFONY_ENV=test
            "${php}" "${composer}" install --optimize-autoloader --prefer-dist
            "${php}" bin/console doctrine:schema:drop --force
            "${php}" bin/console doctrine:schema:create
            "${php}" bin/console doctrine:fixtures:load --quiet

            # Correct permissions on cache and log directories.
            fixPermissions

            # Clear the cache (hardcore mode)
            clearCache hardcore
        fi
    ;;
    -p | --prod)
        if initialChecks; then
            printf "Setting up for production...\n"

            # Find composer or download it.
            getComposer

            # Setup Symfony and dependencies.
            export SYMFONY_ENV=prod
            "${php}" "${composer}" install --optimize-autoloader --prefer-dist --no-dev

            # Correct permissions on cache and log directories.
            fixPermissions

            # Check for and run migrations.
            runMigrations

            # Clear the cache (hardcore mode)
            clearCache
        fi
    ;;
    *)
        # Invalid option, show the help text.
        helpText
    ;;
esac