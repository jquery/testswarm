#! /bin/bash
git pull --quiet
git submodule --quiet foreach git pull --quiet
bin/uaparser ua-parser:update
if phpunit --stop-on-failure &> /dev/null; then
    git commit --quiet -a -m "Scheduled resource update" && git push --quiet origin master
else
    echo "Could not update resources"
fi
