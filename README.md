# Overview
Welcome to the Official Repository for WinTenBot Telegram Bot.<br>
This engine bot is a new engine bot that currently use in  [WinTenBot](https://t.me/WinTenBot) and still tested for new feature update in [WinTenBetaBot](https://t.me/WinTenBetaBot). This project is open source, everyone is free to use this source code. Source code based on the PHP framework [Php Telegram Bot](https://github.com/php-telegram-bot/core).
<br>
# Our Community
Join one of the following Groups and Channels. In this group we test and debug our Bot for improve reability.
- [Windows 10 Community ID](https://t.me/WinTenGroup)
- [Telegram Bot API](https://t.me/TgBotID)
- [Mac OSX Indonesia](https://t.me/MacOSXIDGroup)
- [Redmi 5A (Riva) ID](https://t.me/Redmi5AID)

[WinTenBetaBot](https://t.me/WinTenBetaBot) is restricted as Beta, you can't add to your group. Please use [WinTenBot](https://t.me/WinTenBot) as Stable. **Beta is for testing purposes only**

We have special Developer edition which include Dev integration like git push event, build event, ci/cd, etc. he called [WinTen Dev Bot](https://WinTenDevBot). You can add it into your group. 

# How to deploy or run locally
How to run or install this Project for your own bot.

Make sure you have installed composer and at least PHP 7.0

0. Clone this repo, then open in terminal
1. Run **composer install**
2. Copy paste all file under Resources\Config and root which with .example subfix (example bots.php.example to bots.php) and fill config match your nessesary.
3. Run mode
   - Poll mode
        1. If you want to run locally type **php multi-poll.php [id bot in bots.php]**
        2. Wait until get `Bot is must ready!`
   - Webhook
        1. Note: single hook mode is deprecated, please use multi-hook
        2. If you want to run as webhook, you must upload this project to your server with HTTPS (SSL) support. for speed reason, we recommended using VPS.
        3. fill at least one bots entry in file Resources\Config\bots.php. you can call using https://yourserver.com/multi-hook.php?id=your_bot_id 
        4. Set webhook using postman or directly using browser or curl in terminal.
        
Note: One bot can't run on poll and hook mode same time.

Feel free ask about deployment to [Telegram Bot API](https://t.me/TgBotID) or [WinTen Dev](https://t.me/WinTenDev)

# Powered by
- [JetBrains PhpStorm](https://www.jetbrains.com/phpstorm)
- [Php Telegram Bot](https://github.com/php-telegram-bot/core)
- [Telegram Bot API](https://core.telegram.org/bots/api)
