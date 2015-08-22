<?PHP
include __DIR__ . '/common/config.php';
include __DIR__ . '/common/functions.php';

// 模拟登陆
login_post(E_LOGIN_URL, E_COOKIE_PATH, [
    'UserName' => E_USERNAME,
    'Password' => E_PASSWORD
]);

// 模拟获取学习列表
$content = get_content(sprintf(E_LIST_URL, 1000, 1), E_COOKIE_PATH);
@unlink(E_COOKIE_PATH);

// 保存列表
$list = json_decode($content, true);
foreach ($list['rows'] as $item) {
    import_word([
        'word'            => trim(strip_tags($item['cell'][1])),
        'phonetic_symbol' => trim(strip_tags($item['cell'][2])),
        'explanation'     => trim(str_replace('<br>', "\n", $item['cell'][3])),
        'grade'           => $item['cell'][4],
        'add_time'        => date('Y-m-d', strtotime($item['cell'][5])),
        'email_count'     => 0,
        'unique_id'       => $item['cell'][6]
    ]);
}

echo count($list['rows']);
