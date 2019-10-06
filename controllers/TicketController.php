<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andrey
 * Date: 03.10.2019
 * Time: 14:12
 */

namespace app\controllers;


use app\models\Film;
use app\models\Ticket;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Покупка и бронирование билетов на фильм
 *
 * Class TicketController
 * @package app\controllers
 *
 *
 */
class TicketController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className()
        ];
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors; // TODO: Change the autogenerated stub
    }

    public function beforeAction($action)
    {
        if(in_array($action->id, ['process-buy','buy','reserved'])) {
            $row = \Yii::$app->request->get('row');
            $col = \Yii::$app->request->get('col');
            $film = \Yii::$app->request->get('film');

            //проверка чтобы ряд и место не выходили за рамки зала
            if (!isset(\Yii::$app->params['schema'][$row - 1]) || (isset(\Yii::$app->params['schema'][$row - 1]) && (\Yii::$app->params['schema'][$row - 1] < $col))) {
                \Yii::$app->response->data = ['status' => false, 'code' => 402, 'error' => 'Row or place goes beyond hall'];
                \Yii::$app->end(200);
            }
            //проверка наличия фильма
            $mFilm = Film::findOne(['ID' => $film]);
            if (empty($mFilm)) {
                \Yii::$app->response->data = ['status' => false, 'code' => 403, 'error' => 'Film not found'];
                \Yii::$app->end(200);
            }
        }
        return parent::beforeAction($action);
    }

    /**
     * Получить список купленных или забронированных билетор
     *
     * @api {get} /ticket/all/:film Получить список купленных или забронированных билетов
     * @apiName GetTicket
     * @apiGroup Ticket
     *
     * @apiParam {Number} film Идентификатор фильма
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *     [
            {
            "ID": 2,
            "row": 1,
            "col": 1,
            "option": 1,
            "films_ID": 1
            }
        ]
     * @apiError UserNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 200 Ok
     *     {
     *       []
     *     }
     */
    public function actionAll($film)
    {
        return Ticket::find()->where(['films_ID' => $film])->all();
    }

    /**
     *  Окончание покупки билета
     *
     * @param int $film Идентификатор фильма
     * @param int $row номер ряда
     * @param int $col номер места
     *
     * @return array ['status' => false, 'code' => 403, 'error' => 'Film not found']
     */
    public function actionBuy($film, $row, $col)
    {

        $mTicket = Ticket::findOne(['row' => $row, 'col' => $col, 'films_ID' => $film]);
        if(empty($mTicket) || !($mTicket->option &= Ticket::$FLAG_PROCESS_BUY)){
            $this->response['code'] = 401;
            $this->response['error'] = 'Ticket not reserved before buy';
        }else {
            $mTicket->buy();
            $this->response['status'] = $mTicket->save();
            if ($this->response['status'] == false) {
                $this->response['error'] = $mTicket->errors;
            }
        }
        return $this->sendResponse();
    }
    /**
     * Резервирование билета для предоставления возможности покупки пользователю, после определенного истечении времени резервирование
     * сбрасывается
     *
     * @param int $film Идентификатор фильма
     * @param int $row номер ряда
     * @param int $col номер места
     *
     * @return array ['status' => false, 'code' => 403, 'error' => 'Film not found']
     */
    public function actionProcessBuy($film, $row, $col)
    {

        //проверка если указаное место уже в процессе покупки
        $mTicket = Ticket::findOne(['films_ID' => $film, 'row' => $row, 'col' => $col]);
        if($mTicket){
            return ['status' => false, 'code' => 404, 'error' => 'Ticket in progress buying'];
        }else{
            $mTicket = new Ticket();
        }

        $mTicket->films_ID = $film;
        $mTicket->row = $row;
        $mTicket->col = $col;
        $mTicket->processBuy();
        $this->response['status'] = $mTicket->save();

        return $this->sendResponse();
    }
    /**
     * Бронирование билета
     *
     * @param int $film Идентификатор фильма
     * @param int $row номер ряда
     * @param int $col номер места
     *
     * @return array ['status' => false, 'code' => 403, 'error' => 'Film not found']
     */
    public function actionReserved($film, $row, $col)
    {
        //проверка если указаное место уже в процессе покупки
        $mTicket = Ticket::findOne(['films_ID' => $film, 'row' => $row, 'col' => $col]);
        if($mTicket){
            return ['status' => false, 'code' => 404, 'error' => 'The ticket is occupied'];
        }else{
            $mTicket = new Ticket();
        }

        $mTicket->films_ID = $film;
        $mTicket->row = $row;
        $mTicket->col = $col;
        $mTicket->reserved();
        $this->response['status'] = $mTicket->save();
        return $this->sendResponse();
    }

}