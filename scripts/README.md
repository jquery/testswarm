Maintenance scripts
===================

Create projects
---------------

Before being able to submit jobs (via web interface or api),
you must first create projects that will give you auth credentials
to be used when requesting testswarm instance.

```shell
php scripts/manageProject.php --create --id="jQuery" --display-title="jQuery.is(awesome)"
```

[Add jobs](https://github.com/jquery/testswarm/blob/master/scripts/addjob/README.md)
---------

