Maintenance scripts
===================

Create projects
---------------

Jobs are organized by project. Start by creating your first project, like so:

```shell
php scripts/manageProject.php --create --id="jquery" --display-title="jQuery"
```

The script will provide you with the project's authentication token. Use this token to
[submit jobs](./addjob/README.md) to your TestSwarm instance (via web interface or API).
