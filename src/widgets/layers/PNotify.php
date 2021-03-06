<?php

namespace lo\modules\noty\widgets\layers;

use Yii;
use yii\helpers\Json;

/**
 * Class PNotify
 * @package lo\modules\noty\widgets\layers
 *
 * This widget should be used in your main layout file as follows:
 * ---------------------------------------
 *  use lo\modules\noty\widgets\Wrapper;
 *
 *  echo Wrapper::widget([
 *      'layerClass' => 'lo\modules\noty\widgets\layers\PNotify',
 *      'options' => [
 *          'styling' => 'brighttheme', // jqueryui, bootstrap3, brighttheme
 *          'delay' => 3000,
 *          'icon' => true,
 *          'remove' => false,
 *          'shadow' => true,
 *          'mouse_reset' => true,
 *          'buttons' =>[
 *              'closer' => true,
 *              'sticker' => true
 *          ]
 *
 *          // and more for this library...
 *      ],
 *  ]);
 * ---------------------------------------
 */
class PNotify extends Layer implements LayerInterface
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        PNotifyAsset::register($this->getView());
        $this->overrideConfirm();
    }

    /**
     * @inheritdoc
     */
    public function getNotification($type, $message, $options)
    {
        $options['title'] = $this->getTitle($type);
        $options['type'] = $type;
        $options['text'] = $message;
        $options = Json::encode($options);

        return "new PNotify($options);";
    }

    /**
     * Override System Confirm
     */
    public function overrideConfirm(){
        if ($this->overrideSystemConfirm) {
            $title = Yii::t('noty', 'Confirmation Needed');

            $this->view->registerJs("
                yii.confirm = function(message, ok, cancel) {
                    (new PNotify({
                        title: '$title',
                        text: message,
                        icon: 'glyphicon glyphicon-question-sign',
                        hide: false,
                        confirm: {
                            confirm: true
                        },
                        buttons: {
                            closer: false,
                            sticker: false
                        },
                        history: {
                            history: false
                        },
                        addclass: 'stack-modal',
                        stack: {'dir1': 'down', 'dir2': 'right', 'modal': true }
                    })).get().on('pnotify.confirm', function() {
                        !ok || ok();
                    }).on('pnotify.cancel', function() {
                        !cancel || cancel();
                    });
                }
            ");
        }
    }
}
