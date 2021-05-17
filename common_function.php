<?php

// XSS(クロスサイトスクリプティング)を防ぐ為の関数
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

// 0だった場合は空文字を返す
function h_digit($d)
{
    if ($d === 0) {
        return "";
    } else {
        return h((string) $d);
    }
}
