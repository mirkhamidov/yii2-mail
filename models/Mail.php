<?php

namespace mirkhamidov\mail\models;

use mirkhamidov\mail\models\base\Mail as BaseMail;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Mail
 * @package mirkhamidov\mail\models
 *
 * @property array $config
 */
class Mail extends BaseMail
{
    public $moreData = [];

    public $setid;

    public $recipient;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['moreData', 'setid', 'config'], 'safe'],
        ]);
    }

    public function setConfig($value)
    {
        $this->config_data = Json::encode(ArrayHelper::merge($this->getConfig(), $value));
    }

    public function getConfig()
    {
        if (!empty($this->config_data)) {
            return Json::decode($this->config_data);
        }
        return [];
    }

    public function mailParamsDefault()
    {
        $return = [];
        foreach ($this->mailParams as $param) {
            $_defaultValue = $param->value_default;
            if (empty($_defaultValue)) {
                $_defaultValue = '[EMPTY]';
            }
            $return[$param->key] = $_defaultValue;
        }
        return $return;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailLogs()
    {
        return $this->hasMany(MailLog::className(), ['mail_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailParams()
    {
        return $this->hasMany(MailParams::className(), ['mail_id' => 'id']);
    }
}
