#!/bin/bash
PPCI=vendor/equinton/ppci/install
cp -f $PPCI/env .
cp -f env .env
cp -Rf $PPCI/app/Config/* app/Config/
cp -Rf $PPCI/app/Libraries/* app/Libraries/
cp -Rf $PPCI/public/* public/
cp -f $PPCI/.gitignore .
cp -Rf $PPCI/locales .
mkdir app/Views/templates
cp -Rf $PPCI/Views/templates/* app/Views/templates/
echo "add javascript components"
cd public/display
npm update
cd ../..
mkdir -p writable/templates_c
mkdir writable/temp
touch writable/templates_c/.gitkeep
touch writable/temp/.gitkeep
chmod -R g+w writable
chgrp -R www-data writable
echo "generate encryption keys"
openssl genpkey -algorithm rsa -out id_app -pkeyopt rsa_keygen_bits:2048
openssl rsa -in id_app -pubout -out id_app.pub
chown www-data id_app