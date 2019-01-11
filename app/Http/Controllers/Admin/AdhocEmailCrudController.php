<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AdhocEmailRequest as StoreRequest;
use App\Http\Requests\AdhocEmailRequest as UpdateRequest;
use App\Events\AdhocEmailCreated;

/**
 * Class AdhocEmailCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AdhocEmailCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\AdhocEmail');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/adhoc-email');
        $this->crud->setEntityNameStrings('Adhoc Email', 'Adhoc Emails');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        // $this->crud->setFromDb();

        // List Columns
        $this->crud->addColumn([
            'name' => 'subject',
            'type' => 'text',
            'label' => 'Subject'
        ]);

        $this->crud->addColumn([
            'name' => 'body',
            'type' => 'text',
            'label' => 'Body'
        ]);

        $this->crud->addColumn([
            // n-n relationship (with pivot table)
            'label' => 'Destinations', // Table column heading
            'type' => 'select_multiple',
            'name' => 'destinations', // the method that defines the relationship in your Model
            'entity' => 'destinations', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Company", // foreign key model
        ]);

        $this->crud->addColumn([
            'name' => 'created_at', // The db column name
            'label' => 'Sent On', // Table column heading
            'type' => 'date',
            'format' => DATE_TIME_FORMAT, // use something else than the base.default_date_format config value
        ]);

        //FORM FIELDS
        $this->crud->addField([       // Select2Multiple = n-n relationship (with pivot table)
            'label' => 'Destinations',
            'type' => 'select2_multiple',
            'name' => 'destinations', // the method that defines the relationship in your Model
            'entity' => 'destinations', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Company", // foreign key model
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
            'select_all' => true, // show Select All and Clear buttons?
        ]);

        $this->crud->addField([
            'name' => 'subject',
            'type' => 'text',
            'label' => 'Subject'
        ]);

        $this->crud->addField([
            'name' => 'body',
            'type' => 'ckeditor',
            'label' => 'Body'
        ]);

        // add asterisk for fields that are required in AdhocEmailRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
        $this->crud->removeButtonFromStack('update', 'line');
        $this->crud->removeButtonFromStack('delete', 'line');
        $this->crud->setListView('crud::customize.list');
        $this->crud->removeButtonFromStack('create', 'top');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        event(new AdhocEmailCreated($this->crud->entry));
        return redirect()->route('crud.adhoc-email.index');
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
