<?php

/**
 * This is the model class for table "_yii_isc_company".
 *
 * The followings are the available columns in table '_yii_isc_company':
 * @property integer $id
 * @property string $name
 * @property string $company_name
 * @property string $address
 * @property string $city
 * @property integer $state
 * @property integer $zip
 * @property string $contact
 * @property string $email
 * @property string $email_title
 * @property string $logo
 * @property string $email_txt
 */
class Tenants extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return IscCompany the static model class
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
		return '_yii_tenants';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, address, city, state, zip, contact, email', 'required'),
			array('state, zip', 'numerical', 'integerOnly'=>true),
			array('name, address, city', 'length', 'max'=>255),
			array('contact', 'length', 'max'=>20),
			array('email', 'length', 'max'=>60),
            array('email','userExists', 'skipOnError'=>true),
			array('name, address, city, state, zip, contact, email', 'safe', 'on'=>'search'),
		);
	}
     public function userExists($attribute,$params){       
            $user = User::model()->findByAttributes(array('email'=>$this->email));
            if(empty($user)){
                $user = new User();
                $this->addError('email','No user found with this email');                
            }
    }
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'modules' => array(self::HAS_MANY,'ModuleAssignments',array('tenant_id'=>'id') ),
            'customers'=>array(self::HAS_MANY, 'Customers', 'tenant_id'),
            'vendors'=>array(self::HAS_MANY, 'Vendors', 'tenant_id'),
            'TelcoConfig'=>array(self::HAS_ONE, 'TelcoConfig', 'tenant_id'),
            'Trunks'=>array(self::HAS_MANY, 'AvailableTrunks', 'tenant_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
            'name' => 'Name',
			'address' => 'Address',
			'city' => 'City',
			'state' => 'State',
			'zip' => 'Zip',
			'contact' => 'Contact No.',
			'email' => 'Email',
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

		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state);
		$criteria->compare('zip',$this->zip);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('email',$this->email,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}