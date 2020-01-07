<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

require_once __DIR__ .'/../helper.php';

class b01110011_recaptcha extends CModule
{
    protected $LOC_PREFIX;
    protected $FILE_PREFIX;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ .'/version.php';

        $this->LOC_PREFIX = bx_loc_prefix();
        $this->FILE_PREFIX = bx_file_prefix();

        $this->MODULE_ID = bx_module_id();
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage($this->LOC_PREFIX .'MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage($this->LOC_PREFIX .'MODULE_DESC');

        $this->PARTNER_NAME = Loc::getMessage($this->LOC_PREFIX .'PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage($this->LOC_PREFIX .'PARTNER_URI');
    }

    public function DoInstall()
    {
        global $APPLICATION;

        if ($this->isVersionD7())
        {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

            ModuleManager::registerModule($this->MODULE_ID);
        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage($this->LOC_PREFIX .'INSTALL_ERROR_VERSION'));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage($this->LOC_PREFIX .'INSTALL_TITLE'), $this->GetPath() .'/install/step.php');
    }

    public function DoUninstall()
    {
        $request = Application::getInstance()->getContext()->getRequest();

        switch ($request['step'])
        {
            case null:
            case 1:

                $APPLICATION->IncludeAdminFile(Loc::getMessage($this->LOC_PREFIX .'UNINSTALL_TITLE'), $this->GetPath() .'/install/unstep.php');
            
            break;
            case 2:

                $this->UnInstallFiles();
                $this->UnInstallEvents();
        
                if ($request['savedata'] != 'Y')
                    $this->UnInstallDB();
        
                ModuleManager::unRegisterModule($this->MODULE_ID);

                $APPLICATION->IncludeAdminFile(Loc::getMessage($this->LOC_PREFIX .'UNINSTALL_TITLE'), $this->GetPath() .'/install/unstep2.php');
            
            break;
        }
    }

    /**
     * Проверяем версию ядра
     */
    public function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

    /**
     * Получаем путь до папки модуля
     */
    public function GetPath($withoutDocumentRoot = false)
    {
        if ($withoutDocumentRoot)
        {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        }
        else
        {
            return dirname(__DIR__);
        }
    }

    /**
     * Устанавливаем таблицы базы данных
     */
    public function InstallDB()
    {

    }

    /**
     * Удаляем установленные таблицы
     */
    public function UnInstallDB()
    {
        Option::delete($this->MODULE_ID); // удаляем настройки модуля
    }

    /**
     * Добавляем события
     */
    public function InstallEvents()
    {
        // $EventManager = EventManager::getInstance();

        // проверка на спам
        // $EventManager->registerEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID, 'GoogleCaptcha', 'initCheckSpam');

        // инициализация js
        // $EventManager->registerEventHandler('main', 'OnEpilog', $this->MODULE_ID, 'GoogleCaptcha', 'initJS');
    }
    
    /**
     * Убираем добавленные события
     */
    public function UnInstallEvents()
    {
        // $EventManager = EventManager::getInstance();

        // проверка на спам
        // $EventManager->unRegisterEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID, 'GoogleCaptcha', 'initCheckSpam');

        // инициализация js
        // $EventManager->unRegisterEventHandler('main', 'OnEpilog', $this->MODULE_ID, 'GoogleCaptcha', 'initJS');
    }

    /**
     * Копируем нужные файлы в систему
     */
    public function InstallFiles()
    {
        // копируем компоненты
        if (Directory::isDirectoryExists($path = $this->GetPath() .'/install/components'))
        {
            CopyDirFiles($path, $_SERVER['DOCUMENT_ROOT'] .'/bitrix/components', true, true);
        }

        // копируем админские файлы
        if (Directory::isDirectoryExists($path = $this->GetPath() .'/install/admin'))
        {
            if ($dir = opendir($path))
            {
                $exclusionFiles = ['.', '..'];

                while ($item = readdir($dir) !== false)
                {
                    if (in_array($item, $exclusionFiles)) continue;

                    copy($path .'/'. $item, $dest = $_SERVER['DOCUMENT_ROOT'] .'/bitrix/admin/'. $this->FILE_PREFIX . $item);

                    // для замены айди модуля в файлах install/admin
                    if (file_exists($dest))
                    {
                        $content = file_get_contents($dest);
                        $content = str_replace('%%MODULE_ID%%', $this->MODULE_ID, $content);
                        file_put_contents($dest, $content);
                    }
                }

                closedir($dir);
            }
        }
    }

    /**
     * Удаляем файлы
     */
    public function UnInstallFiles()
    {
        // удаляем компоненты
        if (Directory::isDirectoryExists($path = $this->GetPath() .'/install/components'))
        {
            if ($dir = opendir($path))
            {
                $exclusionFiles = ['.', '..'];

                while ($item = readdir($dir) !== false)
                {
                    if (in_array($item, $exclusionFiles)) continue;
                    if (!is_dir($path .'/'. $item)) continue;

                    Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] .'/bitrix/components/'. $item);
                }

                closedir($dir);
            }
        }

        // удаляем админские файлы
        if (Directory::isDirectoryExists($path = $this->GetPath() .'/install/admin'))
        {
            if ($dir = opendir($path))
            {
                $exclusionFiles = ['.', '..'];

                while ($item = readdir($dir) !== false)
                {
                    if (in_array($item, $exclusionFiles)) continue;

                    File::deleteFile($_SERVER['DOCUMENT_ROOT'] .'/bitrix/admin/'. $this->FILE_PREFIX . $item);
                }

                closedir($dir);
            }
        }
    }
}