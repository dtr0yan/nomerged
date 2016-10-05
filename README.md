Nomerged
--------
This script allows to execute command
```
git for-each-ref --format='%(committerdate) %09 %(authorname) %09 %(refname)' --no-merged <some-branch>
```
and send email to users to notify them that they need to do something with not merged branches (they own).

Just clone project, add config.php (example in config.sample.php) and execute (or add this command to cron)
```
php /paht/to/process.php
```
Anyone can forget what for and when we create some branches.

Hope this script will be helpful.
