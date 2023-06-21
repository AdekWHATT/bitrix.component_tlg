<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class telegram_integration extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->MODULE_ID = "telegram_integration";
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("TELEGRAM_INTEGRATION_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("TELEGRAM_INTEGRATION_MODULE_DESC");
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        RegisterModuleDependences("main", "OnAfterUserAdd", $this->MODULE_ID, "TelegramIntegration", "OnAfterUserAddHandler");
        // Дополнительные действия при установке модуля
    }

    public function DoUninstall()
    {
        UnRegisterModuleDependences("main", "OnAfterUserAdd", $this->MODULE_ID, "TelegramIntegration", "OnAfterUserAddHandler");
        ModuleManager::unRegisterModule($this->MODULE_ID);
        // Дополнительные действия при удалении модуля
    }
}
