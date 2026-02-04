#!/bin/bash

cd "$(dirname "${BASH_SOURCE[0]}")"
cd ..

echo Checking Composer...
composer install --prefer-dist
composer validate --strict
echo Outdated:
composer outdated --direct

echo
echo Checking Code Syntax...
vendor/bin/parallel-lint --exclude .git --exclude vendor .

echo
echo Code Analysis...
vendor/bin/phpstan.phar analyse

echo
echo Checking Code Styles...
vendor/bin/phpcs -sp --standard=ruleset.xml SourceCode
vendor/bin/phpcs -sp --standard=ruleset.tests.xml Tests

echo
echo Running Automated Tests
vendor/bin/phpunit --configuration Tests/phpunit.xml

if [[ $1 == "release" ]] ; then
	echo "release Is set!"

	rm -rf Documentation
	phpDocumentor.phar --setting="graphs.enabled=true" -d SourceCode -t Documentation --ignore "SourceCode/vendor/"

	if [ -z "$2" ]
	then
		echo "No tag specified"
		exit 1
	fi

	if [ -z "$3" ]
	then
		echo "No message specified"
		exit 1
	fi

	git checkout main
	git merge --no-ff development

	git tag $2
	git push --tags
	git push --all

	gh release create $2 --notes $3
fi
