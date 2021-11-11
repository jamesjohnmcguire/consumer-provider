#!/bin/bash
cd ../../SourceCode

echo PHP code styles
../vendor/bin/phpcs -sp --standard=ruleset.xml .

cd ..

if [[ $1 == "release" ]] ; then
	echo "release Is set!"

	rm -rf Documentation
	phpDocumentor.phar --setting="graphs.enabled=true" -d SourceCode -t Documentation
fi
