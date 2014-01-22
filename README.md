# Menta Sample Project

## Getting started

    git clone git://github.com/AOEmedia/Menta_SampleProject.git Menta_SampleProject
    cd Menta_SampleProject
    ./composer.phar install

    # Creating directory for HTML reports
    cd Tests
    mkdir -p ../build/reports

    # change settings in ../conf/demo.ff.vmhost.xml or create a new configuration file

    # run single test
    ../bin/phpunit --configuration=../conf/demo.ff.vmhost.xml General/ScreenshotsTest.php

    # run all tests
    ../bin/phpunit --configuration=../conf/demo.ff.vmhost.xml ../vendor/aoemedia/menta/lib/Menta/Util/CreateTestSuite.php