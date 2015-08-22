# How to export database structure?

``` bash
    mysqldump --opt --extended-insert -u root -p -d eudic_notify \
        study_list \
        | sed 's/AUTO_INCREMENT=[0-9]*\s//g' > eudic_notify.sql
```
