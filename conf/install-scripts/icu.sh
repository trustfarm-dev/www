#! /bin/bash

# ----------------------------------------------------------------------------------------------------------------------
# This file is part of {@link https://github.com/MovLib MovLib}.
#
# Copyright © 2013-present {@link https://movlib.org/ MovLib}.
#
# MovLib is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public
# License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
# version.
#
# MovLib is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY# without even the implied warranty
# of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License along with MovLib.
# If not, see {@link http://www.gnu.org/licenses/ gnu.org/licenses}.
# ----------------------------------------------------------------------------------------------------------------------

# ----------------------------------------------------------------------------------------------------------------------
# "libmemcached" installation script.
#
# LINK:       https://github.com/trondn/libmemcached
# AUTHOR:     Richard Fussenegger <richard@fussenegger.info>
# COPYRIGHT:  © 2013 MovLib
# LICENSE:    http://www.gnu.org/licenses/agpl.html AGPL-3.0
# LINK:       https://movlib.org/
# SINCE:      0.0.1-dev
# ----------------------------------------------------------------------------------------------------------------------

source $(pwd)/inc/conf.sh

if [ ${#} == 1 ]; then
  VERSION=${1}
else
  VERSION="52-1"
  msginfo "No version string supplied as argument, using default version ${VERSION}!"
fi

NAME="icu"
source ${ID}uninstall.sh
svn export http://source.icu-project.org/repos/icu/icu/tags/release-${VERSION}/ ${NAME}-${VERSION}
cd ${SD}/${NAME}-${VERSION}
./source/runConfigureICU Linux/gcc
source ${ID}install.sh