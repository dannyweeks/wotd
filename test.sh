#!/usr/bin/env bash

#!/usr/bin/env sh

requiredPhpVersion='7.0'

initialChecks() {
    errors=false

    # Gotta have PHP >= $requiredPhpVersion installed and in your path.
    php="$(which php)"

    if [ ! "${php}" ] || [ "$(${php} -r "echo (version_compare(phpversion(), '$requiredPhpVersion', '<='));")" ]; then
        # If PHP isn't available or doesn't meet the minimum requirements, we can't continue.
        printf "PHP >= %s needs to be installed and in your path, to run this project.\n" "${requiredPhpVersion}"

        errors=true
    fi

    # Gotta have PHP_CodeSniffer in your project's dev dependencies.
    if [ ! -f vendor/bin/phpcs ]; then
        printf "You need PHP_CodeSniffer to run this script.\n"
        printf "Did you run setup.sh with the \"-t\" or \"-d\" option?\n"

        errors=true
    fi

    # Gotta have PHPUnit as well.
    if [ ! -f vendor/bin/phpcs ]; then
        printf "You need PHPUnit to run this script.\n"
        printf "Did you run setup.sh with the \"-t\" or \"-d\" option?\n"

        errors=true
    fi

    # Gotta have PHPCBF as well.
    if [ ! -f vendor/bin/phpcbf ]; then
        printf "You need PHPCBF to run this script.\n"
        printf "It should be included with PHP_CodeSniffer.\n"
        printf "Did you run setup.sh with the \"-t\" or \"-d\" option?\n"

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

runSniffs() {
    printf "Running PHP_CodeSniffer to check for coding standard violations...\n"

    sniffs=false

    if "${php}" vendor/bin/phpcs --standard=PSR2 src; then
        sniffs=true
    fi
}

runTests() {
    printf "Running unit/functional tests...\n"

    tests=false

    if "${php}" vendor/bin/phpunit; then
        tests=true
    fi
}

runCbf() {
    printf "Running PHPCBF...\n"

    # Unfortunately, phpcbf returns a non-zero exit code any time issues were fixed, or errors were encountered.
    # Which means, we pretty much have to assume that it went well.
    "${php}" vendor/bin/phpcbf --standard=PSR2 src

    printf "You code should be much prettier now!\n"
    printf "Note: This tool may not have fixed all of the issues.\n"
    printf "You should probably re-run the test suite to be sure.\n"
}

cleanUp() {
    # Remove the temporary file created by PHPCBF if it exists.
    if [ -f "phpcbf-fixed.diff" ]; then
        rm "phpcbf-fixed.diff"
    fi
}

trapUser() {
    printf "\n\nTerminated by user!\n\n"

    cleanup

    exit 1
}

trapGeneral() {
    printf "\n\nUnexpectedly terminated\n\n"

    cleanup

    exit 1
}

trap trapUser INT QUIT

trap trapGeneral HUP TERM

helpText() {
    printf "Usage: %s [option]\n\n\
    Options:\n\
    \t-h, --help\tDisplays this help text.\n\
    \t-r, --run\tRuns the test suite.\n\
    \t-f, --fix\tAutomatically fixes most PSR2 violations.\n" "${0}"
}

# Parse options
case "${1}" in
    -h | --help)
        helpText
    ;;
    -r | --run)
        if initialChecks; then
            runSniffs
            runTests

            if [ "${sniffs}" = "false" ] || [ "${tests}" = "false" ]; then
                printf "\n\nErrors were encountered!\n"
                printf "|----------------------------------------------------------------------------\n"

                if [ "${sniffs}" = "false" ]; then
                    printf "\nPHP_CodeSniffer detected some code standard violations.\n"
                    printf "Most violations can be fixed by running this script with the \"-f\" option.\n"
                fi

                if [ "${tests}" = "false" ]; then
                    printf "\nSome unit/functional tests failed.\n"
                    printf "You'll need to figure out why and fix any issues before this branch will pass a CI build.\n"
                fi

                printf "|----------------------------------------------------------------------------\n"
                printf "This branch will not pass a CI build!\n"
                printf "|----------------------------------------------------------------------------\n"

            else
                printf "\n\nCompleted Successfully!\n"
                printf "|----------------------------------------------------------------------------\n"
                printf "Note this tool may not find all of the problems that could have an effect\n\
later down the road. However, at this time, it was unable to find anything\nthat would stop this branch from passing a \
CI build!\n"
                printf "|----------------------------------------------------------------------------\n"
                printf "This branch should pass a CI build!\n"
            fi
        fi
    ;;
    -f | --fix)
        if initialChecks; then
            runCbf
        fi
    ;;
    *)
        helpText
    ;;
esac

exit 0
