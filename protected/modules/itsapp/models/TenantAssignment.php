<?php

/**
 * This is the model class for table "_yii_tenant_assignment".
 *
 * The followings are the available columns in table '_yii_tenant_assignment':
 * @property integer $id
 * @property integer $user_id
 * @property integer $tenant_id
 */
class TenantAssignment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TenantAssignment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '_yii_tenant_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, tenant_id', 'required'),
			array('user_id, tenant_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, tenant_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        return array(
            'tenant' => array(self::BELONGS_TO, 'Tenants', 'tenant_id'),
            'user'=>array(self::BELONGS_TO, 'User', 'user_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'tenant_id' => 'Tenant',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		if(is_array($this->tenant_id) AND isset($this->tenant_id)){
            $criteria->addInCondition('tenant_id',$this->tenant_id,'AND'); 
            $criteria->group = 'user_id';

        }else{
            $criteria->compare('tenant_id',$this->tenant_id);
            if(!isset($this->user_id))
            $criteria->group = 'user_id';
        }
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}