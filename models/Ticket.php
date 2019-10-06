<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tickets".
 *
 * @property int $ID
 * @property int $row ряд
 * @property int $col место
 * @property int $option Флаги куплено/забронированно
 * @property int $films_ID
 *
 * @property Film $films
 */
class Ticket extends \yii\db\ActiveRecord
{

    public static $FLAG_BUY = 1;
    public static $FLAG_RESERVED = 2;
    public static $FLAG_PROCESS_BUY = 4;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tickets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['row', 'col', 'option', 'films_ID'], 'integer'],
            [['films_ID'], 'required'],
            [['films_ID'], 'exist', 'skipOnError' => true, 'targetClass' => Film::className(), 'targetAttribute' => ['films_ID' => 'ID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'row' => 'Row',
            'col' => 'Col',
            'option' => 'Option',
            'films_ID' => 'Film ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilms()
    {
        return $this->hasOne(Film::className(), ['ID' => 'films_ID']);
    }

    /**
     * Купить билет
     */
    public function buy()
    {
        $this->option = self::$FLAG_BUY;
    }

    /**
     * В процессе покупки
     */
    public function processBuy()
    {
        $this->option = self::$FLAG_PROCESS_BUY;
    }

    /**
     * Забронирован билет
     */
    public function reserved()
    {
        $this->option = self::$FLAG_RESERVED;
    }
}
