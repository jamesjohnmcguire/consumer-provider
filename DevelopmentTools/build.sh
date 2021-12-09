#!/bin/bash
cd ..

composer install

echo composer outdated packages:
composer outdated

cd SourceCode

echo PHP code styles
../vendor/bin/phpcs -sp --standard=ruleset.xml .

cd ..

if [[ $1 == "release" ]] ; then
	echo "release Is set!"

	rm -rf Documentation
	phpDocumentor.phar --setting="graphs.enabled=true" -d SourceCode -t Documentation

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
