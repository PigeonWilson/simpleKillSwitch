# simpleKillSwitch
Uses php and mysql

- fixed-in-time contract payload execution
- dns resolution kill switch
- canary web page kill switch
- remote call kill switch with token authentication
- WAP self-suicide mode (execute the payload if logs pattern matching flag up, not implemented yet)

## functionalities
execute a code payload when a set of condition are met
has a setup mode, which is disabled by default, to show error during setup

## purpose
Weaponized canary-like API and web page examples

## setup
see asapLoader.php for configuration and ASAP.sql to import a basic database example

## LICENSE
MIT

### language(s)
php 8.1, mysql
