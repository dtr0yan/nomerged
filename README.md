Nomerged
--------
This script allow to execute command
```
git for-each-ref --format='%(committerdate) %09 %(authorname) %09 %(refname)' --no-merged <some-branch>
```
and send email to users to notify them that they need to do something with not merged branches (they own).

Every of us can forget what and when we create some branches.

Hope this script will help someone.