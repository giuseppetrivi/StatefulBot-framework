# 👾 StatefulBot - a PHP Telegram Bot framework

![Static Badge](https://img.shields.io/badge/PHP-8.2.12-%23777BB4?style=flat&logo=php&logoSize=auto&link=https%3A%2F%2Fwww.php.net%2F)
![Static Badge](https://img.shields.io/badge/Composer-2.6.5-%23885630?style=flat&logo=composer&logoSize=auto&link=https%3A%2F%2Fgetcomposer.org%2F)

![Static Badge](https://img.shields.io/badge/irazasyed%2Ftelegrambotsdk-3.13-%2326A5E4?style=flat&logo=packagist&logoSize=auto&link=https%3A%2F%2Fgetcomposer.org%2F)
![Static Badge](https://img.shields.io/badge/SergeyTsalkov%2Fmeekrodb-2.5-%23003B57?style=flat&logo=packagist&logoSize=auto&link=https%3A%2F%2Fgithub.com%2FSergeyTsalkov%2Fmeekrodb)
![Static Badge](https://img.shields.io/badge/phpunit%2Fphpunit-11.5-%23777BB4?style=flat&logo=packagist&logoSize=auto&link=https%3A%2F%2Fdocs.phpunit.de%2Fen%2F11.5%2F)

<br>

**StatefulBot** is a PHP framework to easily create a Telegram Bot. This framework is **built specifically to solve the state management problems**!

Through the [wiki](https://github.com/giuseppetrivi/telegram-bot-basic-project-structure/wiki) you'll find out how to use it, some basic architectural aspects and all the implementation specifications. <br>
You'll finally understand how to use the framework to easily create complex Telegram Bots.
<br>
<br>

## 🤔 Understanding the state management problem

When you are dealing with Telegram Bot development, you notice that each bot request is stateless, so every request is new to the Telegram Bot webhook. If you want to create a complex procedure in your Telegram Bot, you need to handle it manually, checking that each request is in the right moment of procedure and responding accordingly, changing the state.

To give <u>a practical example</u>, imagine you want to develop a Telegram Bot that asks you for your name, your nickname and your email address, in this specific order, one by one. 
Starting from a `/start` command, the bot sends you the message `Send your name`, so you send your name `Giuseppe`. When you send your name, the hook basically doesn't know that you are in this specific procedure, but it sees every command as equally new. So you need to keep the state of your procedure and check it every time a message (or a request, more generally) is sent to the bot, to check if the message sent is consistent with the specific state of the bot, and then updating the state.

Implementing this logic manually can be challenging, but with this framework the main aspects are handled automatically. You only need to follow some basic rules (explained in the [wiki](https://github.com/giuseppetrivi/telegram-bot-basic-project-structure/wiki))
<br>

---
## 🛠️ Installation

The steps to get this framework ready and to start developing your Telegram Bot are:
- Download and install [PHP](https://www.php.net/) and [Composer](https://getcomposer.org/). You'll use Composer to eventually update packages.
- Clone this repository on your server (or locally) with the following command: 
```bash
git clone https://github.com/giuseppetrivi/StatefulBot-framework.git
```
- Create a Telegram Bot via [@BotFather](https://t.me/BotFather) ([here is a simple guide](https://deepakmohansingh.medium.com/how-to-build-a-telegram-bot-using-php-absolute-beginner-guide-f4262174442d)). 
- Set the webhook to the `hook.php` file and fill the `config.json` file with necessary information (like Telegram Bot API token).
- Before making the first changes, choose your Telegram Bot name and execute the following command (using Git Bash) to change the main namespace name (better explained in [Chapter 3 of the wiki](https://github.com/giuseppetrivi/telegram-bot-basic-project-structure/wiki/Autoloaders)), putting it in the place of `<HERE_YOUR_BOT_NAME>`:
```bash
cd /path/to/project/
find . -type f -not -path ".git" -exec sed -i 's/CustomBotName/<HERE_YOUR_BOT_NAME>/g' {} \;
```

<br>

