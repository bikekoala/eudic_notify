<?PHP
include __DIR__ . '/common/config.php';
include __DIR__ . '/common/functions.php';

// 获取待发送单词
$word = get_one_except_word();
print_r($word);

// 发送邮件
$is_send = send_mail(
    $word['word'],
    $word['phonetic_symbol'],
    $word['explanation']
);

// 发送次数+1
if ($is_send) {
    update_data($word['unique_id'], ['@email_count' => 'email_count+1']);
}
