<?php
/*
In the name of god
source: file uploader
dev: TuranFilm
v1
@ilqar_TurkSoy
*/
date_default_timezone_set('Asia/Tehran');
error_reporting(0);
set_time_limit(0);
// ------------------ { Your Config } ------------------ //
$Config = [
    'api_token' => "1685549909:AAHKaNwoO2NMvjNRh-4MHJyWwZx7JerY-rk", // توکن خود را اینجا وارد کنید
    'admin' => [965221088, 682942717, 0000000000], // ایدی عددی ادمین ها را اینجا وارد کنید
    'channel' => "TuranMovies" // ایدی کانال را بدون @ اینجا وارد کنید
];
$Database = [
    'dbname' => "3762121_wpress853e5d20", // نام دیتابیس را اینجا وارد کنید
    'username' => "3762121_wpress853e5d20", //یوزرنیم دیتابیس را اینجاوارد کنید
    'password' => "Qaradag1@2#3&" //پسورد دیتابیس را اینجا وارد کنید
];
$MySQLi = mysqli_connect('localhost', $Database['username'], $Database['password'], $Database['dbname']);
// ------------------ { Functions } ------------------ //
function Bot($method, $datas = []) {
    global $Config;
    $curl = curl_init('https://api.telegram.org/bot'.$Config['api_token'].'/'.$method);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $datas,
        CURLOPT_CUSTOMREQUEST => 'POST',
    ]);
    $response = json_decode(curl_exec($curl)); 
}
function RandomString() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = null;
    for ($i = 0; $i < 9; $i++) {
        $randstring .= $characters[
            rand(0, strlen($characters))
        ];
    }
    return $randstring;
}
function  getUserProfilePhotos($from_id) {
    global $Config;
    $url = 'https://api.telegram.org/bot'.$Config['api_token'].'/getUserProfilePhotos?user_id='.$from_id;
    $result = file_get_contents($url);
    $result = json_decode ($result);
    $result = $result->result;
    return $result;
}
function convert($size){
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.['', 'K', 'M', 'G', 'T', 'P'][$i].'B';
}
function doc($name) {
    if ($name == "document") {
        return "پرونده ( سند )";
    }
    elseif ($name == "video") {
        return "ویدیو";
    }
    elseif ($name == "photo") {
        return "عکس";
    }
    elseif ($name == "voice") {
        return "ویس";
    }
    elseif ($name == "audio") {
        return "موزیک";
    }
    elseif ($name == "sticker") {
        return "استیکر";
    }
}
// ------------------ { Variables } ------------------ //
$update = json_decode(file_get_contents('php://input'));
if (isset($update->message)) {
    $message = $update->message;
    $text = $message->text;
    $tc = $message->chat->type;
    $chat_id = $message->chat->id;
    $from_id = $message->from->id;
    $message_id = $message->message_id;
    $first_name = $message->from->first_name;
    $last_name = $message->from->last_name;
    $username = $message->from->username?:'اکانت شما بدون یوزرنیم میباشد ... !';
    $getuserprofile = getUserProfilePhotos($from_id);
}
if (isset($update->callback_query)) {
    $callback_query = $update->callback_query;
    $data = $callback_query->data;
    $tc = $callback_query->message->chat->type;
    $chatid = $callback_query->message->chat->id;
    $fromid = $callback_query->from->id;
    $messageid = $callback_query->message->message_id;
    $firstname = $callback_query->from->first_name;
    $lastname = $callback_query->from->last_name;
    $cusername = $callback_query->from->username;
    $membercall = $callback_query->id;
}
// ------------------ { Connect MySQL } ------------------ //
$user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `id` = '{$from_id}' LIMIT 1"));
// ------------------ { Connect MySQL & Creat Table } ------------------ //
if ($MySQLi->query("SELECT * FROM `user`") == false) {
    mysqli_query($MySQLi, "CREATE TABLE `user` (
        `id` bigint(10) NOT NULL PRIMARY KEY,
        `step` varchar(50) NOT NULL,
        `upload` bigint(10) NOT NULL,
        `code` char(200) NOT NULL
        )"
	);
}
if ($MySQLi->query("SELECT * FROM `dbfile`") == false) {
    mysqli_query($MySQLi, "CREATE TABLE `dbfile` (
        `code` char(250) NOT NULL PRIMARY KEY,
        `file_id` char(200) NOT NULL,
        `file` char(200) NOT NULL,
        `password` char(200) CHARACTER SET utf8mb4 NOT NULL,
        `file_size` bigint(20) NOT NULL,
        `user_id` bigint(20) NOT NULL,
        `date` char(200) NOT NULL,
        `time` char(200) NOT NULL,
        `dl` bigint(20) NOT NULL
        )"
    );
}
// ------------------ { Informations } ------------------ //
$rank = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getChatMember?chat_id=@'.$Config['channel'].'&user_id='.$from_id), true)['result']['status'];
$crank = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getChatMember?chat_id=@'.$Config['channel'].'&user_id='.$fromid), true)['result']['status'];
$usernamebot = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getMe'), true)['result']['username'];
// ------------------ { Keyboards } ------------------ //
if (in_array($from_id, $Config['admin'])) {
    $menu = json_encode(['keyboard'=>[
        [['text' => "📂 تاریخچه اپلود"], ['text' =>"🎫 حساب کاربری"]],
        [['text' => "🔐 تنظیم پسورد"], ['text' => "🗑 حذف فایل"]],
        [['text' => "👤 مدیریت"], ['text' => "🗂 کد پیگیری فایل"]]
        ], 'resize_keyboard' => true
    ]);
} else {
    $menu = json_encode(['keyboard'=>[
        [['text' => "📂 تاریخچه اپلود"], ['text' =>"🎫 حساب کاربری"]],
        [['text' => "🔐 تنظیم پسورد"], ['text' => "🗑 حذف فایل"]],
        [['text' => "🗂 کد پیگیری فایل"]]
        ], 'resize_keyboard' => true
    ]);
}
if (in_array($from_id, $Config['admin'])) {
    $panel = json_encode(['keyboard' => [
        [['text' => "👤 امار ربات"]],
        [['text' => "📪 ارسال به همه"], ['text' => "📪 فوروارد به همه"]],
        [['text' => "🔙"]]
        ], 'resize_keyboard' => true
    ]);
    $back_panel = json_encode(['keyboard' => [
        [['text' => "برگشت 🔙"]]
        ], 'resize_keyboard' => true
    ]);
}
// ------------------ { Back Keyboards } ------------------ //
$join = json_encode(['inline_keyboard' => [
    [['text' => "📢 عضویت", 'url' => "t.me/".$Config['channel']]],
    ]
]);
$back = json_encode(['keyboard' => [
    [['text' => "🔙 بازگشت"]],
    ], 'resize_keyboard' => true
]);
$remove = json_encode(['remove_keyboard' => [
    ], 'remove_keyboard' => true
]);
$channel="factweb";
// ------------------ { Start Source } ------------------ //
  $left = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getChatMember?chat_id=@'.$channel.'&user_id='.$from_id))->result->status; 

  if($left !== "left")
{
if (preg_match('/^\/(start)$/i', $text) or $text == "🔙") {
    Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => null
    ]);
    Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👤 سلام <code>$first_name</code>\n🤖 به ربات آپلودر فکت وب خوش امدید!\n\n🏷 اپلود رایگان و دائم فایل ها بدون هیچ محدودیت زمانی !\n\n🚦 شما میتوانید ( عکس , فیلم , گیف , استیکر و ... ) در ربات اپلود کنید همراه با نمایش تعداد دانلود های فایل شما ... !\n\n▪️ شما میتوانید تا سقف دو گیگابایت (2GB) فایل اپلود کنید و لینک فایل خودتون رو دریافت کنید و برای دوستان خود ارسال کنید :\n\n🔐 دقت کنید که میتوانید برای لینک فایل های خودتون رمز بگزارید تا هرکسی نتواند فایلتون رو دانلود کنه , برای دسترسی به فایل وقتی که کاربر با لینک دانلود وارد میشود ربات از او رمز رو درخواست میکند در صورت صحیح بودن رمزی که شما برای فایلتون انتخاب کردید فایل برایش ارسال میشود ... !\n\n📤 همین الان یه فایل بفرس تا آپلود بشه و لذتشو ببر !\n\n🤖 @$usernamebot\n📢 @{$Config['channel']}",
        'reply_to_message_id' => $message_id,
        'parse_mode' => "html",
        'reply_markup' => $menu
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif ($rank == "left") {
    Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال زیر شوید\n\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n\n👇 بعد از « عضویت » مجدد دستور /start را وارد کنید 👇", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);
}
elseif(strpos($text, "/start _") !== false) {
    $idFile = str_replace("/start _", null, $text);
    $dataFile = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `code` = '{$idFile}' LIMIT 1"));
    $dl = number_format($dataFile['dl']);
    $method = $dataFile['file'];
    if ($dataFile['password']) {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ لطفا رمز فایل را ارسال کنید تا فایل برای شما ارسال شود :",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $remove
        ]);
        if (!$user) {
            $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'checkpassword', '0', '{$idFile}')");
        } else {
            $MySQLi->query("UPDATE `user` SET `step` = 'checkpassword', `code` = '{$idFile}' WHERE `id` = '{$from_id}' LIMIT 1");
        }
    } else {
        $dl = number_format($dataFile['dl']);
        $method = $dataFile['file'];
        Bot('send'.$dataFile['file'], [
            'chat_id' => $chat_id,
            "$method" => $dataFile['file_id'],
            'caption' => "📥 تعداد دانلود ها : <code>{$dl}</code>\n🤖 @$usernamebot",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html",
            'reply_markup' => $menu
        ]);
        if (!$user) {
            $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
        } else {
            $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
        }
        $MySQLi->query("UPDATE `dbfile` SET `dl` = `dl` + 1 WHERE `code` = '{$idFile}' LIMIT 1");
    }
}
elseif ($user['step'] == "checkpassword") {
    $dataFile = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `code` = '{$user['code']}' LIMIT 1"));
    if ($text == $dataFile['password']) {
        $dl = number_format($dataFile['dl']);
        $method = $dataFile['file'];
        Bot('send'.$dataFile['file'], [
            'chat_id' => $chat_id,
            "$method" => $dataFile['file_id'],
            'caption' => "📥 تعداد دانلود ها : <code>{$dl}</code>\n🤖 @$usernamebot",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html",
            'reply_markup' => $menu
        ]);
        $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$from_id}' LIMIT 1");
        $MySQLi->query("UPDATE `dbfile` SET `dl` = `dl` + 1 WHERE `code` = '{$dataFile['code']}' LIMIT 1");
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ پسورد اشتباه است , لطفا پسورد صحیح را ارسال کنید :\n🔸 در صورت نیاز به منوی اصلی روی /start کلیک کنید ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}

elseif ($text == "🔙 بازگشت") {
    Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🌼 به منوی اصلی ربات برگشتیم \n\n🎉 برای استفاده از ربات از دکمه های زیر استفاده کنید",
        'reply_to_message_id' => $message_id,
        'reply_markup' => $menu
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif(strpos($text, "/dl_") !== false) {
    $idFile = str_replace("/dl_", null, $text);
    $query = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = '{$from_id}' and `code` = '{$idFile}' LIMIT 1"));
    if ($query) {
        $dl = number_format($query['dl']);
        $method = $query['file'];
        Bot('send'.$query['file'], [
            'chat_id' => $chat_id,
            "$method" => $query['file_id'],
            'caption' => "📥 تعداد دانلود ها : <code>{$dl}</code>\n▪️ شناسه : <code>{$query['code']}</code>\n\n📥 https://t.me/".$usernamebot."?start=_".$idFile."\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n🤖 @$usernamebot\n📢 @{$Config['channel']}",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html"
        ]);
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , این فایل در دیتابیس موجود نمیباشد یا فایل مال شخص دیگری میباشد و  شما اجازه دسترسی به این فایل را ندارید ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
    
}
elseif ($text == "🗑 حذف فایل") {
    Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "▪️لطفا شناسه فایل خود را ارسال کنید :\n📍 توجه کنید که بعد از فرستادن شناسه , فایل همان لحظه پاک میشود پس لطفا الکی شناسه فایلتون رو ارسال نکنید و فقط در صورت نیاز استفاده بکنید از این بخش ... !",
        'reply_to_message_id' => $message_id,
        'reply_markup' => $back
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'delete', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'delete' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif($user['step'] == "delete") {
    $query = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = '{$from_id}' and `code` = '{$text}' LIMIT 1"));
    if ($query) {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "✔️ فایل با موفقیت حذف شد ... !",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $menu
        ]);
$MySQLi->query("UPDATE `user` SET `upload` = `upload` - 1 WHERE `id` = '{$from_id}' LIMIT 1");
        $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
        $MySQLi->query("DELETE FROM `dbfile` WHERE `code` = '{$text}' and `user_id` = '{$from_id}' LIMIT 1");
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , این فایل در دیتابیس موجود نمیباشد یا فایل مال شخص دیگری میباشد و  شما اجازه دسترسی به این فایل را ندارید ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}
elseif ($text == "🗂 کد پیگیری فایل") {
    Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "▪️لطفا شناسه فایل خود را ارسال کنید :",
        'reply_to_message_id' => $message_id,
        'reply_markup' => $back
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'checkfile', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'checkfile' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif ($user['step'] == "checkfile") {
    $query = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = '{$from_id}' and `code` = '{$text}' LIMIT 1"));
    if ($query) {
        $file_size = convert($query['file_size']);
        $file = doc($query['file']);
        $time = $query['time'];
        $date = $query['date'];
        $password = $query['password']?$query['password']:'این فایل بدون رمز عبور است ... !';
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ شناسه فایل شما : <code>$text</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$file_size</b> \n▪️ نوع فایل : <b>$file</b>\n🔐 رمز فایل : <code>$password</code>\n🕓 تاریخ و زمان اپلود : <b>".$date." - ".$time."</b>"."\nلینک شتراک گذاری فایل:\n\n📥 https://t.me/".$usernamebot."?start=_".$query['code']."\n\n📢 @{$Config['channel']}\n🤖 @$usernamebot",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html",
            'reply_markup' => $menu
        ]);
        $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , این فایل در دیتابیس موجود نمیباشد یا فایل مال شخص دیگری میباشد و  شما اجازه دسترسی به این فایل را ندارید ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}
elseif ($text == "🎫 حساب کاربری") {
    if ($getuserprofile->photos[0][0]->file_id != null) {
        Bot('sendphoto', [
            'chat_id' => $chat_id,
            'photo' => $getuserprofile->photos[0][0]->file_id,
            'caption' => "💭 حساب کاربری شما در ربات ما :\n\n 📤 تعداد فایل های اپلود شده توسط شما : <b>{$user['upload']}</b> \n👤 نام کانت شما : <code>$first_name</code>\n🌟 یوزنیم اکانت شما : <code>$username</code>\n\n🤖 @$usernamebot\n📢 @{$Config['channel']}",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html",
            'reply_markup' => json_encode(['inline_keyboard' => [
                [['text' => "▪️ تعداد فایل اپلود شده", 'callback_data' => "none"], ['text' => $user['upload'], 'callback_data' => "none"]]
                ]
            ])
        ]);
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "💭 حساب کاربری شما در ربات ما :\n\n 📤 تعداد فایل های اپلود شده توسط شما : <b>{$user['upload']}</b> \n👤 نام کانت شما : <code>$first_name</code>\n🌟 یوزنیم اکانت شما : <code>$username</code>\n\n🤖 @$usernamebot\n📢 @{$Config['channel']}",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html",
            'reply_markup' => json_encode(['inline_keyboard' => [
                [['text' => "▪️ تعداد فایل اپلود شده", 'callback_data' => "none"], ['text' => $user['upload'], 'callback_data' => "none"]]
                ]
            ])
        ]);
    }
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif($text == "📂 تاریخچه اپلود") {
    $query = mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = {$from_id}");
    $num = mysqli_num_rows($query);
    if($num > 0) {
        $result = "📂 تاریخچه اپلود های شما :\n📍 تعداد فایل های اپلود شده ی شما : $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
        $cnt = ($num >= 10)?10:$num;
        for ($i = 1; $i <= $cnt; $i++) {
            $fetch = mysqli_fetch_assoc($query);
            $id = $fetch['code'];
            $file_size = convert($fetch['file_size']);
            $file = doc($fetch['file']);
            $time = $fetch['time'];
            $date = $fetch['date'];
            $password = $fetch['password']?$fetch['password']:'این فایل بدون رمز عبور است ... !';
            $result .= $i.". 📥 /dl_".$id.PHP_EOL."💾 ".$file_size.PHP_EOL."▪️ نوع فایل : <b>$file</b>".PHP_EOL."🔐 رمز فایل : <code>$password</code>".PHP_EOL."🕓 تاریخ و زمان اپلود : <b>".$date." - ".$time."</b>".PHP_EOL."➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖".PHP_EOL;
        }
        if($num > 10){
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => $result,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html",
                'reply_markup' => json_encode(['inline_keyboard' => [
                    [['text' => "▪️ صفحه ی بعدی", 'callback_data' => "Dnext_10"]]
                    ]
                ])
            ]);
        } else {
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => $result,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html",
            ]);
        }
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ تاریخچه اپلود شما خالی میباشد ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}
elseif ($text == "🔐 تنظیم پسورد") {
    Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => '▪️ لطفا شناسه فایل خود را ارسال کنید :',
        'reply_to_message_id' => $message_id,
        'parse_mode' => "html",
        'reply_markup' => $back
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'setid', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'setid' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif($user['step'] == "setid") {
    $query = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = '{$from_id}' and `code` = '{$text}' LIMIT 1"));
    if ($query) {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️لطفا پسورد دلخواه رو بفرستید تا فایل شما قفل شود :",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $back
        ]);
        $MySQLi->query("UPDATE `user` SET `code` = '{$text}', `step` = 'setpassword' WHERE `id` = '{$from_id}' LIMIT 1");
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , این فایل در دیتابیس موجود نمیباشد یا فایل مال شخص دیگری میباشد و  شما اجازه دسترسی به این فایل را ندارید ... !\n🔐 لطفا شناسه فایل را صحیح بفرستید :",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $back
        ]);
    }
}
elseif ($user['step'] == "setpassword") {
    Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "✔️ با موفقیت فایل شما قفل شد ... !",
        'reply_to_message_id' => $message_id,
        'reply_markup' => $menu
    ]);
    $MySQLi->query("UPDATE `dbfile` SET `password` = '{$text}' WHERE `code` = '{$user['code']}' LIMIT 1");
    $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$from_id}' LIMIT 1");
}
elseif(strpos($data, "Dnext_") !== false) {
    $last_id = str_replace('Dnext_', null, $data);
    $query = mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = '{$fromid}'");
    $num = mysqli_num_rows($query);
    $result = "📂 تاریخچه اپلود های شما :\n📍 تعداد فایل های اپلود شده ی شما : $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
    $records = [];
    while ($fetch = mysqli_fetch_assoc($query)) {
        $records[] = $fetch;
    }
    if($last_id + 10 < $num){
        $endponit = $last_id + 10;
    } else {
        $endponit = $num;
    }
    for ($i = $last_id; $i < $endponit; $i++) {
        $id = $records[$i]['code'];
        $file_size = convert($records[$i]['file_size']);
        $file = doc($records[$i]['file']);
        $time = $records[$i]['time'];
        $date = $records[$i]['date'];
        $password = $records[$i]['password']?$records[$i]['password']:'این فایل بدون رمز عبور است ... !';
        $result .= $i.". 📥 /dl_".$id.PHP_EOL."💾 ".$file_size.PHP_EOL."▪️ نوع فایل : <b>$file</b>".PHP_EOL."🔐 رمز فایل : <code>$password</code>".PHP_EOL."🕓 تاریخ و زمان اپلود : <b>".$date." - ".$time."</b>".PHP_EOL."➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖".PHP_EOL;
    }
    if($num > $last_id + 10){
        Bot('editmessagetext', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => $result,
            'parse_mode' => "html",
            'reply_markup' =>  json_encode(['inline_keyboard' => [
                [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_".$endponit], ['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_".$endponit]]
                ]
            ])
        ]);
    } else {
        Bot('editmessagetext', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => $result,
            'parse_mode' => "html",
            'reply_markup' =>  json_encode(['inline_keyboard' => [
                [['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_".$endponit]]
                ]
            ])
        ]);
    }
}
elseif(strpos($data, "Dprev_") !== false) {
    $last_id = str_replace('Dprev_', null, $data);
    $query = mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = '{$fromid}'");
    $num = mysqli_num_rows($query);
    $result = "📂 تاریخچه اپلود های شما :\n📍 تعداد فایل های اپلود شده ی شما : $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
    $records = [];
    while ($fetch = mysqli_fetch_assoc($query)) {
        $records[] = $fetch;
    }
    if($last_id % 10 == 0){
        $endponit = $last_id - 10;
    } else {
        $last_id = $last_id-($last_id % 10);
        $endponit = $last_id;
    }
    for ($i = $endponit - 10; $i <= $endponit; $i++) {
        $id = $records[$i]['code'];
        $file_size = convert($records[$i]['file_size']);
        $file = doc($records[$i]['file']);
        $time = $records[$i]['time'];
        $date = $records[$i]['date'];
        $password = $records[$i]['password']?$records[$i]['password']:'این فایل بدون رمز عبور است ... !';
        $result .= $i.". 📥 /dl_".$id.PHP_EOL."💾 ".$file_size.PHP_EOL."▪️ نوع فایل : <b>$file</b>".PHP_EOL."🔐 رمز فایل : <code>$password</code>".PHP_EOL."🕓 تاریخ و زمان اپلود : <b>".$date." - ".$time."</b>".PHP_EOL."➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖".PHP_EOL;
    }
    if($num > $last_id and $endponit - 10 > 0) {
        Bot('editmessagetext', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => $result,
            'parse_mode' => "html",
            'reply_markup' =>  json_encode(['inline_keyboard' => [
                [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_".$endponit], ['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_".$endponit]]
                ]
            ])
        ]);
    } else {
        Bot('editmessagetext', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => $result,
            'parse_mode' => "html",
            'reply_markup' =>  json_encode(['inline_keyboard' => [
                [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_".$endponit]]
                ]
            ])
        ]);
    }
}
elseif ($data == "join") {
    if($crank != "left") {
        Bot('EditMessageText', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => "👤 سلام <code>$firstname</code>\n🤖 به ربات آپلودر فکت وب خوش امدید!\n\n🏷 اپلود رایگان و دائم فایل ها بدون هیچ محدودیت زمانی !\n\n🚦 شما میتوانید ( عکس , فیلم , گیف , استیکر و ... ) در ربات اپلود کنید همراه با نمایش تعداد دانلود های فایل شما ... !\n\n▪️ شما میتوانید تا سقف دو گیگابایت (2GB) فایل اپلود کنید و لینک فایل خودتون رو دریافت کنید و برای دوستان خود ارسال کنید :\n\n🔐 دقت کنید که میتوانید برای لینک فایل های خودتون رمز بگزارید تا هرکسی نتواند فایلتون رو دانلود کنه , برای دسترسی به فایل وقتی که کاربر با لینک دانلود وارد میشود ربات از او رمز رو درخواست میکند در صورت صحیح بودن رمزی که شما برای فایلتون انتخاب کردید فایل برایش ارسال میشود ... !\n\n📤 همین الان یه فایل بفرس تا آپلود بشه و لذتشو ببر !\n\n🤖 @$usernamebot\n📢 @{$Config['channel']}",
            'parse_mode' => "html"
        ]);
    } else {
        Bot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "❌ هنوز داخل کانال @$channel عضو نیستید", 
            'message_id' => $messageid,
            'show_alert' => false
        ]);
    }
}
// ------------------ { Panel Admin } ------------------ //
elseif (in_array($from_id, $Config['admin'])) {
	if (strtolower($text) == "/panel" or $text == "👤 مدیریت" or $text == "panel") {
	    Bot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "👤 به منوی مدیریت ربات خود خوش امدید",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $panel
	    ]);
	    $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
	}
	elseif ($text == "برگشت 🔙") {
	    Bot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "▪️ به منوی مدیریت بازگشتید :",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $panel
	    ]);
		$MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");	
	}
	elseif ($text == "👤 امار ربات") {
		$users = mysqli_query($MySQLi, "SELECT `id` FROM `user`");
		$alluser = mysqli_num_rows($users);
		$dbfile = mysqli_query($MySQLi, "SELECT `code` FROM `dbfile`");
		$allfile = mysqli_num_rows($dbfile);
		$time = date('h:i:s');
		$date = date('Y/m/d');
		Bot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "🤖 امار شما در ساعت <code>$time</code> و تاریخ <code>$date</code> به این صورت میباشد : <code>$alluser</code> نفر 👤\n▪️ تعداد فایل های اپلود شده : <code>$allfile</code>",
	        'reply_to_message_id' => $message_id,
	        'parse_mode' => "html",
	        'reply_markup' => $panel
	    ]);
	}
	elseif ($text == '📪 ارسال به همه' ) {
	    Bot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "▪️ لطفا پیام خود را ارسال کنید :",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $back_panel
	    ]);
		$MySQLi->query("UPDATE `user` SET `step` = 'sendtoall' WHERE `id` = '{$from_id}' LIMIT 1");	
	}
	elseif ($user['step'] == 'sendtoall') {
		if ($text != "برگشت 🔙") {
			$query = mysqli_query($MySQLi, "SELECT * FROM `user`");
			foreach($query as $value){
			    Bot('sendmessage', [
			        'chat_id' => $value['id'],
			        'text' => $text
			    ]);
			}
			Bot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "پیام شما با موفقیت برای همه ارسال شد  ✔️",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $panel
			]);
		}
		$MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");	
	}
	elseif ($text == '📪 فوروارد به همه') {
	    Bot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "▪️ لطفا پیام خود را فوروارد کنید :",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $back_panel
		]);
		$MySQLi->query("UPDATE `user` SET `step` = 'fortoall' WHERE `id` = '{$from_id}' LIMIT 1");	
	}
	elseif ($user['step'] == 'fortoall') {
		if ($text != "برگشت 🔙") {
			$query = mysqli_query($MySQLi, "SELECT * FROM `user`");
			foreach($query as $value){
			    Bot('ForwardMessage', [
				    'chat_id' => $value['id'],
				    'from_chat_id' => $chat_id,
				    'message_id'=>$message_id
				]);
			}
			Bot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "پیام شما با موفقیت به همه فوروارد شد ✔️",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $panel
			]);
		}
		$MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");	
	}
}
/*
In the name of god
source: file uploader
dev: factweb team
v1
factweb.ir
@factweb
*/
// ------------------ { Uploarder } ------------------ //
if(isset($message->document)) {
if ($rank != "left") {
    $file_id = $message->document->file_id;
    $file_size = $message->document->file_size;
    $file = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `file_id` = '{$file_id}' LIMIT 1"));
    if ($file_size <= 2048**3) {
        if(!$file) {
            $code = RandomString();
            $size = convert($file_size);
            $time = date('h:i:s');
            $date = date('Y/m/d');
            Bot('senddocument', [
                'chat_id' => $chat_id,
                'document' => $file_id,
                'caption' => "📍 فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n▪️ شناسه فایل شما : <code>$code</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n\nلینک اشتراک گذاری فایل:\n📥 https://t.me/".$usernamebot."?start=_".$code,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html"
            ]);
            $MySQLi->query("INSERT INTO `dbfile` (`code`, `file_id`, `file`, `password`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$code}', '{$file_id}', 'document', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
            if (!$user) {
                $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
            } else {
                $MySQLi->query("UPDATE `user` SET `upload` = `upload` + 1 WHERE `id` = '{$from_id}' LIMIT 1");
            }
        } else {
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !",
                'reply_to_message_id' => $message_id
            ]);
        } 
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , خطا حجم فایل شما بیشتر از یک گیگابایت است ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}else{
Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال زیر شوید\n\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n\n👇 بعد از « عضویت » مجدد دستور /start را وارد کنید 👇", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);
}
}
if(isset($message->video)) {
	if ($rank != "left") {
    $file_id = $message->video->file_id;
    $file_size = $message->video->file_size;
    $file = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `file_id` = '{$file_id}' LIMIT 1"));
    if ($file_size <= 1024**3) {
        if(!$file) {
            $code = RandomString();
            $size = convert($file_size);
            $time = date('h:i:s');
            $date = date('Y/m/d');
            Bot('sendvideo', [
                'chat_id' => $chat_id,
                'video' => $file_id,
                'caption' => "📍 فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n▪️ شناسه فایل شما : <code>$code</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n\nلینک اشتراک گذاری فایل:\n📥 https://t.me/".$usernamebot."?start=_".$code,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html"
            ]);
            $MySQLi->query("INSERT INTO `dbfile` (`code`, `file_id`, `file`, `password`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$code}', '{$file_id}', 'video', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
            if (!$user) {
                $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
            } else {
                $MySQLi->query("UPDATE `user` SET `upload` = `upload` + 1 WHERE `id` = '{$from_id}' LIMIT 1");
            }
        } else {
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !",
                'reply_to_message_id' => $message_id
            ]);
        } 
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , خطا حجم فایل شما بیشتر از یک گیگابایت است ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
    }else{
Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال زیر شوید\n\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n\n👇 بعد از « عضویت » مجدد دستور /start را وارد کنید 👇", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);
}
}
if(isset($message->photo)) {
	if ($rank != "left") {
    $photo = $message->photo;
    $file_id = $photo[count($photo)-1]->file_id;
    $file_size = $photo[count($photo)-1]->file_size;
    $file = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `file_id` = '{$file_id}' LIMIT 1"));
    if ($file_size <= 1024**3) {
        if(!$file) {
            $code = RandomString();
            $size = convert($file_size);
            $time = date('h:i:s');
            $date = date('Y/m/d');
            Bot('sendphoto', [
                'chat_id' => $chat_id,
                'photo' => $file_id,
                'caption' => "📍 فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n▪️ شناسه فایل شما : <code>$code</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n\nلینک اشتراک گذاری فایل:\n📥 https://t.me/".$usernamebot."?start=_".$code,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html"
            ]);
            $MySQLi->query("INSERT INTO `dbfile` (`code`, `file_id`, `file`, `password`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$code}', '{$file_id}', 'photo', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
            if (!$user) {
                $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
            } else {
                $MySQLi->query("UPDATE `user` SET `upload` = `upload` + 1 WHERE `id` = '{$from_id}' LIMIT 1");
            }
        } else {
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !",
                'reply_to_message_id' => $message_id
            ]);
        } 
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , خطا حجم فایل شما بیشتر از یک گیگابایت است ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
    }else{
Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال زیر شوید\n\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n\n👇 بعد از « عضویت » مجدد دستور /start را وارد کنید 👇", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);
}
}
if(isset($message->voice)) {
	if ($rank != "left") {
    $file_id = $message->voice->file_id;
    $file_size = $message->voice->file_size;
    $file = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `file_id` = '{$file_id}' LIMIT 1"));
    if ($file_size <= 1024**3) {
        if(!$file) {
            $code = RandomString();
            $size = convert($file_size);
            $time = date('h:i:s');
            $date = date('Y/m/d');
            Bot('sendvoice', [
                'chat_id' => $chat_id,
                'voice' => $file_id,
                'caption' => "📍 فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n▪️ شناسه فایل شما : <code>$code</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n\nلینک اشتراک گذاری فایل:\n📥 https://t.me/".$usernamebot."?start=_".$code,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html"
            ]);
            $MySQLi->query("INSERT INTO `dbfile` (`code`, `file_id`, `file`, `password`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$code}', '{$file_id}', 'voice', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
            if (!$user) {
                $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
            } else {
                $MySQLi->query("UPDATE `user` SET `upload` = `upload` + 1 WHERE `id` = '{$from_id}' LIMIT 1");
            }
        } else {
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !",
                'reply_to_message_id' => $message_id
            ]);
        } 
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , خطا حجم فایل شما بیشتر از یک گیگابایت است ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
    }else{
Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال زیر شوید\n\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n\n👇 بعد از « عضویت » مجدد دستور /start را وارد کنید 👇", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);
}
}
if(isset($message->audio)) {
	if ($rank != "left") {
    $file_id = $message->audio->file_id;
    $file_size = $message->audio->file_size;
    $file = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `file_id` = '{$file_id}' LIMIT 1"));
    if ($file_size <= 1024**3) {
        if(!$file) {
            $code = RandomString();
            $size = convert($file_size);
            $time = date('h:i:s');
            $date = date('Y/m/d');
            Bot('sendaudio', [
                'chat_id' => $chat_id,
                'audio' => $file_id,
                'caption' => "📍 فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n▪️ شناسه فایل شما : <code>$code</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n\nلینک اشتراک گذاری فایل:\n📥 https://t.me/".$usernamebot."?start=_".$code,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html"
            ]);
            $MySQLi->query("INSERT INTO `dbfile` (`code`, `file_id`, `file`, `password`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$code}', '{$file_id}', 'audio', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
            if (!$user) {
                $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
            } else {
                $MySQLi->query("UPDATE `user` SET `upload` = `upload` + 1 WHERE `id` = '{$from_id}' LIMIT 1");
            }
        } else {
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !",
                'reply_to_message_id' => $message_id
            ]);
        } 
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , خطا حجم فایل شما بیشتر از یک گیگابایت است ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
    }else{
Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال زیر شوید\n\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n\n👇 بعد از « عضویت » مجدد دستور /start را وارد کنید 👇", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);
}
}
if(isset($message->sticker)) {
	if ($rank != "left") {
    $file_id = $message->sticker->file_id;
    $file_size = $message->sticker->file_size;
    $file = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `file_id` = '{$file_id}' LIMIT 1"));
    if ($file_size <= 1024**3) {
        if(!$file) {
            $code = RandomString();
            $size = convert($file_size);
            $time = date('h:i:s');
            $date = date('Y/m/d');
            Bot('sendsticker', [
                'chat_id' => $chat_id,
                'sticker' => $file_id,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html"
            ]);
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "📍 فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n▪️ شناسه فایل شما : <code>$code</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n\nلینک اشتراک گذاری فایل:\n📥 https://t.me/".$usernamebot."?start=_".$code,
                'parse_mode' => "html"
            ]);
            $MySQLi->query("INSERT INTO `dbfile` (`code`, `file_id`, `file`, `password`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$code}', '{$file_id}', 'sticker', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
            if (!$user) {
                $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
            } else {
                $MySQLi->query("UPDATE `user` SET `upload` = `upload` + 1 WHERE `id` = '{$from_id}' LIMIT 1");
            }
        } else {
            Bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !",
                'reply_to_message_id' => $message_id
            ]);
        } 
    } else {
        Bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , خطا حجم فایل شما بیشتر از یک گیگابایت است ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
    }else{
Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال زیر شوید\n\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n\n👇 بعد از « عضویت » مجدد دستور /start را وارد کنید 👇", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);
}
}
}
else{
  Bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال زیر شوید\n\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n📣 @{$Config['channel']} 📣 @{$Config['channel']}\n\n👇 بعد از « عضویت » مجدد دستور /start را وارد کنید 👇", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);  
}
//-----------------------------//
unlink("error_log");

?>
