<?php
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;

Loc::loadMessages(__FILE__);

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$moduleId = 'telegram_integration';

Loader::includeModule($moduleId);

if ($request->isPost() && check_bitrix_sessid()) {
    $bots = array();

    $botTokens = $request->getPost('bot_token');
    $botNames = $request->getPost('bot_name');

    if (is_array($botTokens) && is_array($botNames) && count($botTokens) === count($botNames)) {
        foreach ($botTokens as $index => $botToken) {
            $botToken = trim($botToken);
            $botName = trim($botNames[$index]);

            if (!empty($botToken) && !empty($botName)) {
                $bots[] = array(
                    'token' => $botToken,
                    'name' => $botName,
                );
            }
        }
    }

    $botsSerialized = serialize($bots);
    Option::set($moduleId, 'bots', $botsSerialized);
}

$bots = Option::get($moduleId, 'bots', "");
$bots = unserialize($bots);
?>

<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request->get('mid'))?>&lang=<?=LANGUAGE_ID?>">
    <?=bitrix_sessid_post()?>
    <table class="edit-table">
        <tr>
            <td><?=Loc::getMessage("TELEGRAM_INTEGRATION_BOT_TOKEN")?></td>
            <td><?=Loc::getMessage("TELEGRAM_INTEGRATION_BOT_NAME")?></td>
        </tr>
        <?php foreach ($bots as $index => $bot): ?>
            <tr>
                <td><input type="text" name="bot_token[]" value="<?=htmlspecialcharsbx($bot['token'])?>"></td>
                <td><input type="text" name="bot_name[]" value="<?=htmlspecialcharsbx($bot['name'])?>"></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><input type="text" name="bot_token[]"></td>
            <td><input type="text" name="bot_name[]"></td>
        </tr>
    </table>
    <br>
    <input type="submit" name="save" value="<?=Loc::getMessage("MAIN_SAVE")?>">
</form>
