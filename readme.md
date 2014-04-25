# Menta Sample Project

All test should work in demo magento 1.8 with sample data
## Getting started
    # Install magento 1.8 with sample data. Default currency "US$"
    http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/installing_magento_via_shell_ssh
    # Create admin, frontend (firstname Test, lastname User) and soap user.
    # Change settings in conf/defaults.xml or create a new configuration file


    git clone git://github.com/AOEmedia/Menta_SampleProject.git Menta_SampleProject
    cd Menta_SampleProject
    ./composer.phar install

    # Creating directory for HTML reports
    cd Tests
    mkdir -p ../build/reports

    # download and run selenium server
    java -jar selenium-server-standalone-2.39.0.jar

    # run single test
    ../bin/phpunit --configuration=../conf/defaults.xml General/ScreenshotsTest.php

    # run all tests
    ../bin/phpunit --configuration=../conf/defaults.xml ../vendor/aoemedia/menta/lib/Menta/Util/CreateTestSuite.php