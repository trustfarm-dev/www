#!/bin/sh

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
# "Closure" installation script.
#
# LINK:       http://code.google.com/p/closure-compiler/
# AUTHOR:     Richard Fussenegger <richard@fussenegger.info>
# COPYRIGHT:  © 2013 MovLib
# LICENSE:    http://www.gnu.org/licenses/agpl.html AGPL-3.0
# LINK:       https://movlib.org/
# SINCE:      0.0.1-dev
# ----------------------------------------------------------------------------------------------------------------------

# Set working directory and include configuration script.
WD="$(cd `dirname ${0}`; pwd)/"
source ${WD}/inc/conf.sh

SRC="${SD}closure-compiler/"

rm -rf ${SRC}
mkdir ${SRC}
cd ${SRC}
source ${ID}wget.sh "http://dl.google.com/closure-compiler/" "compiler-latest" ".tar.gz"
mv "${SRC}compiler.jar" "${WD}/../../bin/closure-compiler.jar"
rm -rf ${SRC}
movlib fp

LINE=$(msgline)
msgsuccess "${LINE}\nSuccessfully installed Google closure compiler\n${LINE}"
exit 0
