<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%sert}}".
 *
 * @property integer $sert_id
 * @property string $sert_name
 * @property integer $sert_active
 * @property string $sert_template
 * @property string $sert_created
 * @property string $sert_updated
 */
class Sert extends \yii\db\ActiveRecord
{
    public function behaviors() {
        return [
            // дата создания ресурса
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'sert_created',
                'updatedAtAttribute' => 'sert_updated',
                'value' => new Expression('NOW()'),
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sert}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sert_name', ], 'required'],
            [['sert_active'], 'integer'],
            [['sert_template'], 'string'],
            [['sert_created', 'sert_updated'], 'safe'],
            [['sert_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sert_id' => 'Sert ID',
            'sert_name' => 'Название',
            'sert_active' => 'Активен',
            'sert_template' => 'Шаблон',
            'sert_created' => 'Создан',
            'sert_updated' => 'Изменен',
        ];
    }
}
