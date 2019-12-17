<?php

namespace DaveJToews\HasOneSearchField;

use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use DaveJToews\HasOneSearchField\GridFieldHasOneButtonRow;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;

/**
 * Class HasOneSearchField
 */
class HasOneSearchField extends GridField
{

    /**
     * The related object to the parent
     *
     * @var DataObject
     */
    protected $record;

    /**
     * The current parent of the relationship (the base object we are editing)
     *
     * @var DataObject
     */
    protected $parent;

    /**
     * The name of the relation this field is managing
     *
     * @var string
     */
    protected $relation;

    /**
     * HasOneButtonField constructor.
     * @param \SilverStripe\ORM\DataObject $parent
     * @param string $relationName
     * @param string|null $fieldName
     * @param string|null $title
     * @param GridFieldConfig|null $customConfig
     * @param boolean|null $useAutocompleter
     */
    public function __construct(DataObject $parent, $relationName, $list = null, $fieldName = null, $title = null, GridFieldConfig $customConfig = null, $useSearch = true)
    {
        $record = $parent->{$relationName}();
        $this->setRecord($record);
        $this->parent = $parent;
        $this->relation = $relationName;

        Requirements::css("silvershop/silverstripe-hasonefield:client/dist/styles/bundle.css");
        Requirements::javascript("silvershop/silverstripe-hasonefield:client/dist/js/bundle.js");

        $config = GridFieldConfig::create()
            ->addComponent(new GridFieldHasOneButtonRow())
            ->addComponent(new GridFieldSummaryField($relationName))
            ->addComponent(new GridFieldDetailForm())
            ->addComponent(new GridFieldHasOneUnlinkButton($parent, 'buttons-before-right'))
            ->addComponent(new GridFieldHasOneEditButton('buttons-before-right'));

        if ($useSearch) {
            $config->addComponent(new GridFieldAddExistingSearchButton('buttons-before-right'));
        }

        $className = $record->ClassName;
        $list = $list ? $list : $className::get();

        parent::__construct($fieldName ?: $relationName, $title, $list, ($customConfig) ?: $config);
        $this->setModelClass($className);
    }

    /**
     * @return \SilverStripe\ORM\DataObject
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param DataObject|null $record
     */
    public function setRecord($record)
    {
        $this->record = $record ?: singleton(get_class($this->record));
    }

    /**
     * Get the current parent
     *
     * @return DataObject
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the current parent
     *
     * @param DataObject $parent parent of the relationship
     *
     * @return self
     */
    public function setParent(DataObject $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get the name of the relation this field is managing
     *
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Set the name of the relation this field is managing
     *
     * @param string $relation The relation name
     *
     * @return self
     */
    public function setRelation(string $relation)
    {
        $this->relation = $relation;
        return $this;
    }
}
