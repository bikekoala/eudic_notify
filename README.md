# Eudic notify

欧陆词典单词邮件通知脚本

## Requirements

* PHP5.5+
* [PHPMailer](https://github.com/PHPMailer/PHPMailer)

## Deployment Steps:

1.  Include PHPMailer
``` bash
    cd common/
    git clone https://github.com/PHPMailer/PHPMailer.git
```

2.  Add the script to crontab
``` bash
    25,55 8-22 * * * /bin/php eudic_notify/send_email.php
    30 6 * * * /bin/php eudic_notify/sync_study_list.php
```
