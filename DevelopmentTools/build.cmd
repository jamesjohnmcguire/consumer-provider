CD %~dp0
CD ..\SourceCode

ECHO PHP code styles
CALL ..\vendor\bin\phpcs -sp --standard=ruleset.xml .

CD ..
REM Currently, not working on windows - phpdocumentor bug
REM CALL phpdocumentor --setting=graphs.enabled=true -d SourceCode -t Documentation