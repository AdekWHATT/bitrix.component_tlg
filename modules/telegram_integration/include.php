<?php
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class TelegramIntegration
{
    public static function OnAfterUserAddHandler(&$fields)
    {
        $bots = Option::get("telegram_integration", "bots", ""); // Получаем настроенные боты из настроек модуля
        $bots = unserialize($bots); // Распаковываем сериализованные данные

        if (!is_array($bots) || empty($bots)) {
            return;
        }

        foreach ($bots as $bot) {
            $botToken = $bot['token'];

            // Код для отправки данных в Telegram бота с использованием $botToken
        }
    }
}
