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

Make sure you have installed composer and PHP 7.0

0. Clone this repo, then open in terminal
1. Run **composer install**
2. Copy paste all file under Resources\Config and root which with .example subfix (example bot.example.php to bot.php) and fill config match your nessesary
3. If you want to run locally type **php pool.php** (not tested anymore)
4. If you want to run as webhook, you must upload this project to your server with HTTPS (SSL) support. for speed reason, we recommended using VPS.
5. If you want to use multi-hook, you must fill at least one bots entry in file Resources\Config\bots.php. you can call using https://yourserver.com/multi-hook.php?id=your_bot_id
6. Set webhook using postman or directly using browser or curl in terminal.
7. One bot can't run on poll and hook mode.

Feel free ask about deployment to [Telegram Bot API](https://t.me/TgBotID) or [Azhe Kun](https://t.me/Azhe403)

# Thanks to
- [JetBrains PhpStorm](https://www.jetbrains.com/phpstorm)
- [Php Telegram Bot](https://github.com/php-telegram-bot/core)
- [Telegram Bot API](https://core.telegram.org/bots/api)
