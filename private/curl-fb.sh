#!/bin/bash
curl "https://www.facebook.com/search/str/teespring.com/stories-keyword/intersect/stories-live?__mref=message_bubble" -H "accept-encoding: gzip, deflate, sdch" -H "accept-language: vi-VN,vi;q=0.8,fr-FR;q=0.6,fr;q=0.4,en-US;q=0.2,en;q=0.2,zh-CN;q=0.2,zh;q=0.2" -H "upgrade-insecure-requests: 1" -H "user-agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36" -H "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8" -H "cache-control: max-age=0" -H "cookie: _ga=GA1.2.521605205.1440430635; lu=gArsiNWM7i5ZcYJ0LEQf_IUw; datr=nmkcU9ODILUK5anjZy_QhUB0; c_user=1772491568; fr=0eQmY2kqvBGuLT56L.AWWU92B4-G7CEYpLd4ZqJQFHbH8.BTHGnJ.4a.FXs.0.AWUGvV18; xs=153"%"3AAUefw8gCnUVgAw"%"3A2"%"3A1440594320"%"3A20068; csm=2; s=Aa7fD0StmB3pUQE0.BV3bmQ; p=-2; act=1441691553150"%"2F6; presence=EDvF3EtimeF1441691629EuserFA21772491568A2EstateFDsb2F1441691523914Et2F_5b_5dElm2FnullEuct2F1441690884004EtrFnullEtwF767280247EatF1441691629017G441691629481CEchFDp_5f1772491568F19CC; dpr=1.100000023841858" --compressed > /home/www/html/bcdcnt.net/demo.bcdcnt.net/teespring/private/fb-teespring.com.txt
php -q /home/www/html/bcdcnt.net/demo.bcdcnt.net/teespring/private/background.php craw fb teespring.com