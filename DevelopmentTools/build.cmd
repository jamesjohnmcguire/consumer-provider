@ECHO OFF

CD %~dp0
CD ..

ECHO Checking Composer...
CALL composer install --prefer-dist
CALL composer validate --strict
ECHO Outdated:
CALL composer outdated --direct

ECHO .
ECHO Checking code syntax...
CALL vendor/bin/parallel-lint --exclude .git --exclude Support --exclude vendor .

ECHO .
ECHO Code Analysis...
CALL vendor\bin\phpstan.phar.bat analyse

ECHO .
ECHO Checking Code Styles...
CALL vendor\bin\phpcs.bat -sp --standard=ruleset.xml SourceCode
CALL vendor\bin\phpcs.bat -sp --standard=ruleset.tests.xml Tests

ECHO Running Automated Tests
CALL vendor\bin\phpunit.bat --config Tests\phpunit.xml

if "%1" == "release" GOTO release
GOTO end

:release
ECHO Release

REM Currently, not working on windows - phpdocumentor bug
REM CALL phpdocumentor --setting=graphs.enabled=true -d SourceCode -t Documentation --ignore "SourceCode/vendor/"

if "%~2"=="" GOTO error1
if "%~3"=="" GOTO error2

git checkout main
git merge --no-ff development

git tag %2
git push --tags
git push --all

gh release create %2 --notes %3

GOTO end

:error1
ECHO No tag specified
GOTO end

:error2
ECHO No message specified

:end
