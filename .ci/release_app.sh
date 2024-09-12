#! /bin/bash

set -u
set -e

if [ -z ${1} ]; then
	echo "Release version (arg1) not set !"
	exit 1;
fi

SRC_DIR=`dirname $0`"/.."
RELEASE_VERSION=${1}
echo "Release version set to ${RELEASE_VERSION}"

sed -ri 's/(.*)<version>(.+)<\/version>/\1<version>'${RELEASE_VERSION}'<\/version>/g' ${SRC_DIR}/appinfo/info.xml
git commit -am "Release "${RELEASE_VERSION}
git tag ${RELEASE_VERSION}
git push
git push --tags
# Wait a second for Github to ingest our data
sleep 1
cd /tmp
rm -Rf scanner-packaging && mkdir scanner-packaging && cd scanner-packaging

# Download the git file from github
wget https://github.com/e-alfred/nextcloud-scanner/archive/${RELEASE_VERSION}.tar.gz
tar xzf ${RELEASE_VERSION}.tar.gz
mv nextcloud-scanner-${RELEASE_VERSION} scanner

# Drop unneeded files
rm -Rf \
	scanner/js/devel \
	scanner/gulpfile.js \
	scanner/package.json \
	scanner/.ci \
	scanner/.tx \
	scanner/doc

tar cfz scanner-${RELEASE_VERSION}.tar.gz scanner
echo "Release version "${RELEASE_VERSION}" is now ready."
