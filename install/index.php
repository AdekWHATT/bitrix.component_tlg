<?php

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

// Класс модуля
Class telegram extends CModule
{
    var $MODULE_ID = "bitru.telegram"; // Идентификатор модуля
    var $MODULE_VERSION; // Версия модуля
    var $MODULE_VERSION_DATE; // Дата версии модуля
    var $MODULE_NAME; // Название модуля
    var $MODULE_DESCRIPTION; // Описание модуля
    public $minimumRequiredPHPVersion = '7.4';
    public $requiredExtensions = array(
        'curl',
        'mbstring',
        'json',
        'pcre',
        'xml'
    );

    // Конструктор класса
    function __construct()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = isset($arModuleVersion['VERSION']) ? $arModuleVersion['VERSION'] : null;
            $this->MODULE_VERSION_DATE = isset($arModuleVersion['VERSION_DATE']) ? $arModuleVersion['VERSION_DATE'] : null;
        }

        $this->MODULE_NAME = Loc::getMessage('BITRU_TELEGRAM_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BITRU_TELEGRAM_MODULE_DESCRIPTION'); // Описание модуля
        
    }

    
    // Метод выполняется при установке модуля
    function DoInstall()
    {
        global $APPLICATION;

        if (!$this->checkRequirements()) {
            return false;
        }

        try {
            if (!$this->installFiles()) {
                throw new Exception(Loc::getMessage('BITRU_TELEGRAM_FILE_PERMISSION_GENERAL_EXCEPTION'));
            }

            $this->RegisterModule();
            /* $this->installEvents(); */
        } catch (Exception $e) {
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }
        $APPLICATION->IncludeAdminFile("Установка модуля telegram");

        return true;


    }

    // Метод выполняется при удалении модуля
    function DoUninstall()
    {
        global $APPLICATION;

        try {
            if (!$this->uninstallFiles()) {
                throw new Exception(Loc::getMessage('BITRU_TELEGRAM_FILE_PERMISSION_GENERAL_EXCEPTION'));
            }

            /* $this->uninstallEvents(); */
            $this->unRegisterModule();
        } catch (Exception $e) {
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }

        return true;
    }
    

    //проверки выполнения требований перед установкой модуля
    public function checkRequirements() : bool
    {
        global $APPLICATION;
        $errors = array();

        if (phpversion() < $this->minimumRequiredPHPVersion) {
            $errors[] = Loc::getMessage('BITRU_TELEGRAM_PHP_VERSION_EXCEPTION', array(
                '{VERSION}' => phpversion(),
                '{REQUIRED}' => $this->minimumRequiredPHPVersion
            ));
        }

        foreach ($this->requiredExtensions as $extension) {
            if (!extension_loaded($extension)) {
                $errors[] = Loc::getMessage('BITRU_TELEGRAM_MISSING_EXTENSION_EXCEPTION', array(
                    '{EXTENSION}' => $extension,
                ));
            }
        }

        $filenames = array(
            Loader::getDocumentRoot() . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'tools'
        );
        foreach ($filenames as $filename) {
            if (!is_writable($filename)) {
                $errors[] = Loc::getMessage('BITRU_TELEGRAM_FILE_PERMISSION_EXCEPTION', array(
                    '{FILENAME}' => $filename
                ));
            }
        }

        if ($errors) {
            $APPLICATION->ThrowException(implode('<br />', $errors));
            return false;
        }

        return true;
    }
    // Регистрация всего модуля
    function RegisterModule() {
        ModuleManager::registerModule($this->MODULE_ID);
    }
    // Удаление всего модуля
    public function unRegisterModule()
    {
        Option::delete($this->MODULE_ID);
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
?>