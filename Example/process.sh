#!/bin/sh

php vendor/digitalzenworks/consumer-provider/SourceCode/starter.php Process OldDatabase WordPressDatabase $1 $2 > /dev/null 2>&1 &
