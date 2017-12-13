<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Subject;

/**
 * SubjectSearch represents the model behind the search form about `app\models\Subject`.
 */
class SubjectSearch extends Subject
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'taken_by', 'created_by', 'level'], 'integer'],
            [['type', 'description', 'text', 'created_at', 'statuses','updated_at', 'finished_at', 'taken_at', 'statuses', 'comments', 'createdBy.name', 'takenBy.name'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['createdBy.name','takenBy.name', 'statuses']); // TODO: Change the autogenerated stub
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
        $query = Subject::find()->innerJoinWith(['statuses', 'createdBy a'])->joinWith(['takenBy b', 'tasks']);

/*        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);*/

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $query;
        }

        if(!Yii::$app->user->identity->is_admin && !Yii::$app->user->identity->is_chief){
            if(Yii::$app->user->identity->is_it){
                $query->andFilterWhere(['!=','symbol','C']);
            } else {
                $query->andFilterWhere(['=','created_by',Yii::$app->user->id]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cast(created_at as date)' => $this->created_at,
            'cast(finished_at as date)' => $this->finished_at,
            'taken_by' => $this->taken_by,
            'created_by' => $this->created_by,
            'level' => $this->level,
        ]);
        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['!=', 'status', 'R'])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like','a.display_name',$this->getAttribute('createdBy.name')])
            ->andFilterWhere(['=','a.org',Yii::$app->user->identity->org])
            ->andFilterWhere(['like','b.display_name',$this->getAttribute('takenBy.name')])
            ->andFilterWhere(['=','symbol',$this->getAttribute('statuses')])
            ->orderBy(['subjects.id'=>SORT_DESC]);

        return $query;
    }
}
