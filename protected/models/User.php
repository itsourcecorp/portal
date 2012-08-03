<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property integer $group
 * @property integer $managed_by
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $mobile
 * @property string $created
 * @property string $cookie
 * @property string $session
 * @property string $ip
 * @property integer $hardware_id
 * @property string $security_question
 * @property string $security_answer
 * @property integer $status
 * @property integer $ext
 * @property integer $picture_id
 * @property integer $notification
 */
class User extends CActiveRecord
{
    public $repeat_password;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'user';
	}
    public function getSpecialRoles(){
        return array('Root','Admin');
    }
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('firstname, lastname, email,password,security_question,security_answer', 'required','skipOnError'=>true),
            array('repeat_password','passMatch'),
            array('email','unique'),
			array('group, managed_by, hardware_id, status, ext, picture_id, notification', 'numerical', 'integerOnly'=>true),
			array('firstname, lastname, email', 'length', 'max'=>255),
			array('password', 'length', 'max'=>40),
			array('phone, mobile', 'length', 'max'=>15),
			array('cookie, session', 'length', 'max'=>100),
			array('ip', 'length', 'max'=>20),
			array('security_question', 'length', 'max'=>64),
			array('security_answer', 'length', 'max'=>50),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, group, managed_by, firstname, lastname, email, password, phone, mobile, created, cookie, session, ip, hardware_id, security_question, security_answer, status, ext, picture_id, notification', 'safe', 'on'=>'search'),
		);
	}

     public function passMatch($attribute,$params){
            //echo $this->repeat_password;
            //die();
                        $user = User::model()->findByAttributes(array('email'=>$this->email));

            if($attribute=='password'){
  
            }elseif($attribute==='repeat_password'){
               if($this->password!=$this->repeat_password){
                    $this->__unset('repeat_password');               
                    $this->addError('repeat_password','Passwords Must Match');
                }else{
                    $this->clearErrors($attribute);
                } 
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'group' => 'Group',
			'managed_by' => 'Managed By',
            'firstname' => 'Firstname',
			'repeat_password' => 'Confirm Password',
			'lastname' => 'Lastname',
			'email' => 'Email',
			'password' => 'Password',
			'phone' => 'Phone',
			'mobile' => 'Mobile',
			'created' => 'Created',
			'cookie' => 'Cookie',
			'session' => 'Session',
			'ip' => 'Ip',
			'hardware_id' => 'Hardware',
			'security_question' => 'Security Question',
			'security_answer' => 'Security Answer',
			'status' => 'Status',
			'ext' => 'Ext',
			'picture_id' => 'Picture',
			'notification' => 'Notification',
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
		$criteria->compare('group',$this->group);
		$criteria->compare('managed_by',$this->managed_by);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('cookie',$this->cookie,true);
		$criteria->compare('session',$this->session,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('hardware_id',$this->hardware_id);
		$criteria->compare('security_question',$this->security_question,true);
		$criteria->compare('security_answer',$this->security_answer,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('ext',$this->ext);
		$criteria->compare('picture_id',$this->picture_id);
		$criteria->compare('notification',$this->notification);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    public function getTenantRoles($tenant){
        $roles = Yii::app()->authManager->getAuthItems(2,$this->id);
        $tenantroles = array();
        foreach($roles as $role){
            $role = explode('.',$role->name);
            if(count($role)>1 AND $role[0]==$tenant){
                $tenantroles[] = $role[1];
            }
            
        }
        
        
        return $tenantroles;
    }
    public function getQuestions(){
        return array(
        'What is your oldest sibling\'s middle name?'=>
        'What is your oldest sibling\'s middle name?',
        'What was the last name of your third grade teacher?'=>
        'What was the last name of your third grade teacher?',
        'What is your maternal grandmother\'s maiden name?'=>
        'What is your maternal grandmother\'s maiden name?',
        'In what city or town was your first job?'=>
        'In what city or town was your first job?',
        'What is the name of a college you applied to but didn\'t attend?'=>
        'What is the name of a college you applied to but didn\'t attend?');
   }

}