<?php
/**
 * Created by PhpStorm.
 * User: Orsaev K.A
 * Date: 08.09.2017
 * Time: 11:21
 */

namespace app\models;


class UserSearch extends User
{

    public function rules()
    {
        return [
            [['dept_id'], 'integer'],
            [['user_name', 'display_name'], 'string', 'max' => 100],
        ];
    }

    public function search($params) {
        $this->load($params);
        $user = User::find()
            ->andFilterWhere(['like','display_name',$this->display_name])
            ->andFilterWhere(['=','dept_id',$this->dept_id])
            ->orderBy(['id'=>SORT_DESC]);
        return $user;
    }
}