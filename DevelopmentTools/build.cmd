CD %~dp0
CD ..

CALL composer validate --strict
CALL composer install --prefer-dist

ECHO composer outdated packages:
CALL composer outdated

ECHO PHP code styles
php SourceCode\vendor\bin\phpcs -sp --standard=ruleset.xml SourceCode

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
