# Travis

Running your Behat tests with WordPress Behat Extension on travis.

This assumes you have set up (or know how to set up) your GitHub repository with [Travis](https://Travis-ci.org).

We shall using [Composer](https://getcomposer.org/) AND [WP-Cli](http://wp-cli.org/) (installed as a composer dependancy) to install and configure WordPress, as well as Behat and other required dependencies.

**Health warning:** The `ci` directory created below must be committed to version control, but it should not be included on live installs - i.e. don't include it in what you send to the wordpress.org repository).


1. Add the following dependencies to your `composer.json` file:

   ```
   {
     ...
     "require-dev": {
       "wp-cli/wp-cli" : "~0.24",
       "behat/behat": "~3.1.0",
       "behat/mink": "~1.7.1",
       "behat/mink-extension": "~2.0",
       "behat/mink-goutte-driver": "~1.1",
       "behat/mink-selenium2-driver": "~1.3.1",
       "stephenharris/wordpress-behat-extension": "0.3.0"
     },
     ...
   }
   ```

1. Define your `Behat.yml` as follows:

   ```
default:
  suites:
    default:
      contexts:
        - FeatureContext:
        - \StephenHarris\WordPressBehatExtension\Context\WordPressContext
        - \StephenHarris\WordPressBehatExtension\Context\WordPressLoginContext
        - \StephenHarris\WordPressBehatExtension\Context\PostTypes\WordPressPostContext
        - \StephenHarris\WordPressBehatExtension\Context\Terms\WordPressTermContext
        - \StephenHarris\WordPressBehatExtension\Context\Users\WordPressUserContext
        - \StephenHarris\WordPressBehatExtension\Context\Options\WordPressOptionContext
        - \StephenHarris\WordPressBehatExtension\Context\Plugins\WordPressPluginContext
        - \StephenHarris\WordPressBehatExtension\Context\WordPressAdminContext
        - \StephenHarris\WordPressBehatExtension\Context\WordPressEditPostContext
        - \StephenHarris\WordPressBehatExtension\Context\WordPressPostListContext
        - \StephenHarris\WordPressBehatExtension\Context\WordPressMailContext

  extensions:
        StephenHarris\WordPressBehatExtension:
          path: '/tmp/wordpress'
          connection:
            db: 'wordpress'
            username: 'root'
            password: ''
          mail:
            directory: '/tmp/mail'
        Behat\MinkExtension:
            base_url: 'http://localhost:8000'
            files_path: '%paths.base%/features/files/'
            goutte:
              guzzle_parameters:
                             curl.options:
                                CURLOPT_SSL_VERIFYPEER: false
                                CURLOPT_CERTINFO: false
                                CURLOPT_TIMEOUT: 120
                             ssl.certificate_authority: false
            selenium2: ~

   ```


1. Create a directory `ci` in the root of your project and add an (executable) file `ci/install-wordpress.sh`

   This downloads and installs WordPress, and creates a `wp-config.php` file. It leverages `wp-cli` to do this, but this can be done without `wp-cli`.
   
   ```
   #!/bin/bash

   # Exit if anything fails AND echo each command before executing
   # http://www.peterbe.com/plog/set-ex
   set -ex

   if [ $# -lt 3 ]; then
      	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
   	   exit 1
   fi

   # Set up constant
   DB_NAME=$1
   DB_USER=$2
   DB_PASS=$3
   DB_HOST=${4-localhost}
   WP_VERSION=${5-latest}

   WORDPRESS_SITE_DIR=${WORDPRESS_SITE_DIR-/tmp/wordpress}

   # Install database for WordPress
   mysql --user="$DB_USER" --password="$DB_PASS" $EXTRA -e "DROP DATABASE IF EXISTS $DB_NAME";
   mysqladmin --no-defaults create $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA;

   # Download WordPress
   mkdir -p $WORDPRESS_SITE_DIR
   vendor/bin/wp core download --force --version=$WP_VERSION --path=$WORDPRESS_SITE_DIR

   # Create configs
   rm -f ${WORDPRESS_SITE_DIR}wp-config.php
   vendor/bin/wp core config --path=$WORDPRESS_SITE_DIR --dbname=$DB_NAME --dbuser=$DB_USER --dbpass=$DB_PASS --dbhost=$DB_HOST

   # We only run the install command so that we can run further wp-cli commands
   vendor/bin/wp core install --path=$WORDPRESS_SITE_DIR --url="wp.dev" --title="wp.dev" --admin_user="admin" --admin_password="password" --admin_email="admin@wp.dev"

   # Copy our test subject to the plugins directory
   # (You will probably want to create a 'build' version of your plug-in and copy that instead).
   rsync -av --exclude=".*" $TRAVIS_BUILD_DIR ${WORDPRESS_SITE_DIR}wp-content/plugins/
   ```

1. Create an (executable) file `ci/init-behat.sh`
  
   This is only required for tests that require Javascript to execute. It 
   downloads and starts selenium.

   ```
   WORDPRESS_SITE_DIR=${WORDPRESS_SITE_DIR-/tmp/wordpress}

   # Used when waiting for stuff
   NAP_LENGTH=1
   SELENIUM_PORT=4444

   # Wait for a specific port to respond to connections.
   wait_for_port() {
      local PORT=$1
      while echo | telnet localhost $PORT 2>&1 | grep -qe 'Connection refused'; do
        echo "Connection refused on port $PORT. Waiting $NAP_LENGTH seconds..."
        sleep $NAP_LENGTH
      done
   }

   rm -f /tmp/.X0-lock

   Xvfb & export DISPLAY=localhost:0.0

   echo 'start php';
   php -S localhost:8000 -t $WORDPRESS_SITE_DIR -d disable_functions=mail > /dev/null 2>&1 &

   # Start Selenium
   wget http://selenium-release.storage.googleapis.com/2.53/selenium-server-standalone-2.53.1.jar
   java -jar selenium-server-standalone-2.53.1.jar -p $SELENIUM_PORT > /dev/null 2>&1 &

   # Wait for Selenium, if necessary
   wait_for_port $SELENIUM_PORT

   echo 'waiting to start tests...';
   sleep 5
   ```

1. Finally, add your `.travis.yml` file to your project:

   In this example we run the tests on PHP 5.5, and WordPress versions 4.4.4, latest stable version and the nightly version.

   ```
   language: php

   sudo: false

   php:
   - 5.5

   env:
   - WP_VERSION=nightly
   - WP_VERSION=latest
   - WP_VERSION=4.4.4

   matrix:
     allow_failures:
       - env: WP_VERSION=nightly

   before_install:
     - export WORDPRESS_SITE_DIR="/tmp/wordpress/"
     - composer self-update

   install:
     # Install dependencies
     - composer update --no-interaction --prefer-dist;

     # install wordpress
     - bash ./ci/prepare.sh wordpress root '' localhost $WP_VERSION

     # start selenium
     - bash ./ci/pre-behat.sh

   script:
     # Run behat tests.
     - vendor/bin/behat
   ```

Then commit all the files and push to GitHub.

## Examples

The following projects run WordPressBehatExtension on travis

 - [This one!](https://github.com/stephenharris/WordPressBehatExtension)
 - [Event Organiser](https://github.com/stephenharris/Event-Organiser)
 - [Test-Test](https://github.com/stephenharris/test-test) - a dummy WordPress plugin to demonstrate unit, integrate and automated end-to-end testing