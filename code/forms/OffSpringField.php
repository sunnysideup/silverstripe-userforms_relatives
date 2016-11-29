<?php
/**
 *
 * @package userforms
 * @subpackage relatives
 */
class OffSpringField extends FormField
{

    /**
     * @var nameField
     */
    protected $headerField = null;

    /**
     * @var nameField
     */
    protected $nameField = null;

    /**
     * @var dobField
     */
    protected $dobField = null;

    /**
     * @var dobField
     */
    protected $sexField = null;


    public function __construct($name, $title = null, $value = "")
    {
        $this->headerField = new HeaderField($name . '[header]', $title, 4);
        $this->nameField = new TextField($name . '[name]', "Name");
        $this->dobField = new TextField($name . '[dob]', "Date of Birth (e.g. 31/01/1986)");
        $this->sexField = new OptionsetField($name . '[sex]', false, array("female", "male"));
        $this->headerField->addExtraClass("header");
        $this->nameField->addExtraClass("name");
        $this->dobField->addExtraClass("dob");
        $this->sexField->addExtraClass("sexField");

        parent::__construct($name, $title, $value);
    }

    public function setForm($form)
    {
        parent::setForm($form);

        $this->headerField->setForm($form);
        $this->nameField->setForm($form);
        $this->dobField->setForm($form);
        $this->sexField->setForm($form);
    }

    public function Field()
    {
        //Requirements::css();
        //Requirements::javascript();
        return $this->headerField->SmallFieldHolder() . $this->nameField->SmallFieldHolder() . $this->dobField->SmallFieldHolder() . $this->sexField->SmallFieldHolder() . '<div class="clear"><!-- --></div>';
    }

    /**
     * Sets the internal value to ISO date format.
     *
     * @param string|array $val String expects an ISO date format. Array notation with 'date' and 'time'
     *  keys can contain localized strings. If the 'dmyfields' option is used for {@link nameField},
     *  the 'date' value may contain array notation was well (see {@link nameField->setValue()}).
     */
    public function setValue($val)
    {
        if (empty($val)) {
            $this->nameField->setValue(null);
            $this->dobField->setValue(null);
            $this->sexField->setValue(null);
        } else {
            // String setting is only possible from the database, so we don't allow anything but ISO format
            if (is_string($val)) {
                //TO DO
            }
            // Setting from form submission
            elseif (is_array($val) && array_key_exists('name', $val) && array_key_exists('dob', $val) && array_key_exists('sex', $val)) {
                $this->nameField->setValue($val['name']);
                $this->dobField->setValue($val['dob']);
                $this->sexField->setValue($val['sex']);
            } else {
                $this->nameField->setValue($val);
                $this->dobField->setValue($val);
                $this->sexField->setValue($val);
            }
        }
    }

    public function dataValue()
    {
        $valName = $this->nameField->dataValue();
        $valDob = $this->dobField->dataValue();
        $valSex = $this->sexField->dataValue();

        $array = array("name" => $valName, "dob" => $valDob, "sex" => $valSex);
        return implode("|", $array);
    }


    /**
     * @return nameField
     */
    public function getHeaderField()
    {
        return $this->headerField;
    }

    /**
     * @return nameField
     */
    public function getNameField()
    {
        return $this->nameField;
    }

    /**
     * @return dobField
     */
    public function getDobField()
    {
        return $this->dobField;
    }

    /**
     * @return sexField
     */
    public function getSexField()
    {
        return $this->sexField;
    }


    public function validate($validator)
    {
        $dateValid = $this->nameField->validate($validator);
        $timeValid = $this->dobField->validate($validator);
        $sexValid = $this->sexField->validate($validator);

        return ($dateValid && $timeValid && $sexValid);
    }

    public function jsValidation()
    {
        return $this->nameField->jsValidation() . $this->dobField->jsValidation() . $this->sexField->jsValidation() ;
    }

    public function performReadonlyTransformation()
    {
        $field = new OffSpringField_Readonly($this->name, $this->title, $this->dataValue());
        $field->setForm($this->form);
        return $field;
    }
}

/**

 */
class OffSpringField_Readonly extends OffSpringField
{
    protected $readonly = true;

    public function Field()
    {
        $valName = $this->nameField->dataValue();
        $valDob = $this->dobField->dataValue();
        $valSex = $this->sexField->dataValue();
        if ($valDate && $valTime && $valSex) {
            $val = $valName." ".$valDob." ".$valSex;
        } else {
            // TODO Localization
            $val = '<i>(not set)</i>';
        }

        return "<span class=\"readonly\" id=\"" . $this->id() . "\">$val</span>";
    }

    public function jsValidation()
    {
        return null;
    }

    public function validate($validator)
    {
        return true;
    }
}
