<?PHP

/**
 * 模拟登陆
 *
 * @param string $url
 * @param string $cookie
 * @param array $post
 * @return void
 */
function login_post($url, $cookie, $post)
{
    $curl = curl_init(); // 初始化curl模块
    curl_setopt($curl, CURLOPT_URL, $url); // 登录提交的地址
    curl_setopt($curl, CURLOPT_HEADER, 0); // 是否显示头信息
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0); // 是否自动显示返回的信息
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); // 设置Cookie信息保存在指定的文件中
    curl_setopt($curl, CURLOPT_POST, 1); // post方式提交
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post)); // 要提交的信息
    curl_exec($curl); // 执行cURL
    curl_close($curl); // 关闭cURL资源，并且释放系统资源
}

/**
 * 登录成功后获取数据
 *
 * @param string $url
 * @param string $cookie
 * @return string
 */
function get_content($url, $cookie)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); // 读取cookie
    $rs = curl_exec($ch); // 执行cURL抓取页面内容
    curl_close($ch);
    return $rs;
}

/**
 * 发送邮件
 *
 * @param string $word
 * @param string $phonetic_symbol
 * @param string $explanation
 * @return bool
 */
function send_mail($word, $phonetic_symbol, $explanation)
{
    include __DIR__ . '/PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;
    $mail->Host = MAIL_HOST;
    $mail->Port = MAIL_PORT;
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Username = MAIL_USERNAME;
    $mail->Password = MAIL_PASSWORD;

    $mail->From = MAIL_USERNAME;
    $mail->FromName = $word;
    $mail->addAddress(MAIL_USERNAME, 'Pebble');

    $mail->isHTML(true);
    $mail->CharSet='UTF-8';

    $mail->Subject = $explanation;
    $mail->Body    = $phonetic_symbol;

    if ( ! $mail->send()) {
        echo 'Message could not be sent.', PHP_EOL;
        echo 'Mailer Error: ' . $mail->ErrorInfo, PHP_EOL;
        return false;
    } else {
        echo 'Message has been sent.', PHP_EOL;
        return true;
    }
}

/**
 * 初始化PDO
 *
 * @return PDO
 */
function init_pdo()
{
    static $pdo;
    if ( ! $pdo) {
        $pdo = new PDO(PDO_DSN, PDO_USR, PDO_PWD);
        $pdo->exec('SET NAMES UTF8');
    }
    return $pdo;
}

/**
 * 获取一个期望单词
 *
 * @return array
 */
function get_one_except_word()
{
    $sql = 'SELECT *
            FROM `study_list`
            WHERE grade != 5
            GROUP BY add_time
            ORDER BY email_count
            LIMIT 1';
    $sth = init_pdo()->prepare($sql);
    $sth->setFetchMode(PDO::FETCH_ASSOC);
    $sth->execute();
    return $sth->fetch();
}

/**
 * 插入单词
 *
 * @param array $data
 * @return void
 */
function import_word($data)
{
    // 检查记录是否存在
    $sql = sprintf(
        'SELECT id FROM `study_list` WHERE unique_id = "%s"',
        $data['unique_id']
    );
    $sth = init_pdo()->prepare($sql);
    $sth->execute();
    $unique_id = $sth->fetchColumn();

    // 若存在则更新等级信息
    if ($unique_id) {
        update_data($data['unique_id'], ['grade' => $data['grade']]);
    // 不存在则新插入记录
    } else {
        insert_data($data);
    }
}

/**
 * 插入数据
 *
 * @param array $data
 * @return int
 */
function insert_data($data)
{
    foreach ($data as $key => $value) {
        $bindFields[$key] = ':' . $key;
    }
    $sql = 'INSERT IGNORE INTO `study_list` (%s) VALUES (%s)';
    $sql = sprintf(
        $sql,
        implode(',', array_keys($bindFields)),
        implode(',', $bindFields)
    );

    $pdo = init_pdo();
    $sth = $pdo->prepare($sql);
    foreach ($bindFields as $key => $bindKey) {
        $sth->bindValue($bindKey, $data[$key]);
    }
    $sth->execute();
    return (int) $pdo->lastInsertId();
}

/**
 * 更新数据
 *
 * @param int $unique_id
 * @param array $data
 * @return int
 */
function update_data($unique_id, $data)
{
    foreach ($data as $key => $value) {
        if ('@' === substr($key, 0, 1)) {
            $key = substr($key, 1);
            $bindFields[$key] = sprintf('%s=%s', $key, $value);
        } else {
            $bindFields[$key] = sprintf('%s=\'%s\'', $key, $value);
        }
    }

    $sql = 'UPDATE `study_list` SET %s WHERE unique_id="%s"';
    $sql = sprintf(
        $sql,
        implode(',', $bindFields),
        $unique_id
    );

    $sth = init_pdo()->prepare($sql);
    return (int) $sth->execute();
}
