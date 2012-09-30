#set -x
#
# If argument i supplied that version is extracted from git
#
if [ $# -eq 1 ]
then
   VERSION=$1
else
   VERSION=`git describe --tags | cut -d- -f1 | cut -b 2-`
fi

PREFIX=ufds-hm-${VERSION}
PUBLIC_DIR=${PREFIX}/public
DESTDIR=dist
if [ ! -d ${DESTDIR} ]
then
   mkdir ${DESTDIR}
fi

echo "Making $DESTDIR/${PREFIX}.tar.gz"

git archive --format=tar --prefix=${PREFIX}/ -o ${DESTDIR}/${PREFIX}.tar v${VERSION}

mkdir -p ${PUBLIC_DIR}
echo ${VERSION} > ${PUBLIC_DIR}/VERSION
tar -rf ${DESTDIR}/${PREFIX}.tar ${PUBLIC_DIR}/VERSION
rm -fr ${PREFIX}

gzip ${DESTDIR}/${PREFIX}.tar
