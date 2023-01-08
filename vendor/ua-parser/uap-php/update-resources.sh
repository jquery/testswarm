#! /bin/bash

function try_catch() {
    output=`"$@" &>/dev/stdout`
    if [ $? -gt 0 ]; then
        echo -e "Command $@ failed\n\n$output"
        exit 1
    fi
}

try_catch git pull
try_catch git submodule foreach git pull origin master
try_catch bin/uaparser ua-parser:convert uap-core/regexes.yaml
try_catch vendor/bin/phpunit --stop-on-failure
try_catch git commit -a -m "Scheduled resource update"
try_catch git push origin master
new_version=`git tag | sort --version-sort | tail -n 1 | awk -F. -v OFS=. 'NF==1{print ++$NF}; NF>1{if(length($NF+1)>length($NF))$(NF-1)++; $NF=sprintf("%0*d", length($NF), ($NF+1)%(10^length($NF))); print}'`
try_catch git tag $new_version
git push origin $new_version
