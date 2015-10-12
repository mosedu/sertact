<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sert;

/**
 * SertSearch represents the model behind the search form about `app\models\Sert`.
 */
class SertSearch extends Sert
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sert_id', 'sert_active'], 'integer'],
            [['sert_name', 'sert_template', 'sert_created', 'sert_updated'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Sert::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sert_id' => $this->sert_id,
            'sert_active' => $this->sert_active,
            'sert_created' => $this->sert_created,
            'sert_updated' => $this->sert_updated,
        ]);

        $query->andFilterWhere(['like', 'sert_name', $this->sert_name])
            ->andFilterWhere(['like', 'sert_template', $this->sert_template]);

        return $dataProvider;
    }
}
