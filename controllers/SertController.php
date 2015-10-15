<?php

namespace app\controllers;

use Yii;
use app\models\Sert;
use app\models\SertSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;

/**
 * SertController implements the CRUD actions for Sert model.
 */
class SertController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sert models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SertSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sert model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sert model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->actionUpdate(0);
/*
        $model = new Sert();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->sert_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
*/
    }

    /**
     * Updates an existing Sert model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if( $id == 0 ) {
            $model = new Sert();
            $model->loadDefaultValues();
        }
        else {
            $model = $this->findModel($id);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->sert_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Sert model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDraw($id = 0)
    {
        if( $id == 0 ) {
            $model = new Sert();
            $model->loadDefaultValues();
        }
        else {
            $model = $this->findModel($id);
        }

        if( Yii::$app->request->isAjax ) {
            if( $model->load(Yii::$app->request->post()) ) {
                $aValidate = ActiveForm::validate($model);
                Yii::$app->response->format = Response::FORMAT_JSON;
                if( count($aValidate) == 0 ) {
//                    if( $model->save() ) {
//                    }
                }
                return $aValidate;
            }
            else {
                return $this->renderAjax(
                    'draw',
                    [
                        'model' => $model,
                    ]
                );
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->sert_id]);
        } else {
            return $this->render('draw', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Sert model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id); // ->delete();
        $model->sert_active = 0;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Print to pdf
     * @param integer $id
     * @return mixed
     */
    public function actionTopdf($id)
    {
        $model = $this->findModel($id); // ->delete();
        $ret = $this->render('pdf01', [
            'model' => $model,
        ]);
        return $ret;
    }

    /**
     * Finds the Sert model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sert the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sert::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
