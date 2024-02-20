<?php
/**
 * Magicline module for Craft CMS 4.x
 *
 * Jims CRM connection
 *
 * @link      esign.eu
 * @copyright Copyright (c) 2022 dieter vanhove
 */

namespace modules\magicline;

use Craft;
use yii\base\Event;
use craft\web\twig\variables\CraftVariable;
use modules\magicline\services\MagiclineApiService;
use modules\magicline\variables\MagiclineVariable;
use Yii;
use yii\base\Module;

/**
 * Class MagiclineModule
 *
 * @author    dieter vanhove
 * @package   MagiclineModule
 * @since     1.0.0
 *
 */
class Magicline extends Module
{
    public static $instance;

    public function init()
    {
        // Set instance of this module
        self::$instance = $this;

        $this->setComponents([
            'api' => MagiclineApiService::class,
        ]);

        parent::init();

        $this->_registerAliasses();
        $this->_registerControllers();
        $this->_registerVariable();
    }

    private function _registerVariable(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $variable = $event->sender;
            $variable->set('magicline', MagiclineVariable::class);
        });
    }

    public function _registerAliasses()
    {
        Yii::setAlias($this->formatYiiAlias('@' . __NAMESPACE__), '@root/modules/magicline');
    }

    public function formatYiiAlias($alias)
    {
        return str_replace('\\', '/', $alias);
    }

    public function _registerControllers()
    {
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\magicline\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\magicline\\controllers';
        }
    }
}
