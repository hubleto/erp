import React, { Component } from 'react'
import { deepObjectMerge } from "adios/Helper";
import Form, { FormProps, FormState } from 'adios/Form';
import InputVarchar from "adios/Inputs/Varchar";
import InputTags2 from "adios/Inputs/Tags2";
import InputTable from "adios/Inputs/Table";
import FormInput from "adios/FormInput";
import { Column } from 'primereact/column';

interface FormCompanyProps extends FormProps {
  categories?: Array<any>,
}

interface FormCompanyState extends FormState {
  categories: Array<any>,
}

export default class FormCompany<P, S> extends Form<FormCompanyProps, FormCompanyState> {
  static defaultProps: any = {
    model: 'CeremonyCrmApp/Modules/Core/Sandbox/Models/Company',
  }

  props: FormCompanyProps;
  state: FormCompanyState;

  constructor(props: FormCompanyProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCompanyProps) {
    return {
      ...super.getStateFromProps(props),
      categories: props.categories ?? [],
    }
  }

  onBeforeSaveRecord(record) {
    if (record.MAIN_PERSON) {
      record.MAIN_PERSON.id_company = {_useMasterRecordId_: true};
      record.MAIN_PERSON.is_main = true;
    }
    if (record.OTHER_PERSONS) record.OTHER_PERSONS.map((item: any, key: number) => {
      record.OTHER_PERSONS[key].id_company = {_useMasterRecordId_: true};
      record.OTHER_PERSONS[key].is_main = false;
    });

    return record;
  }

  renderTitle(): JSX.Element {
    let categories = [];
    for (let i in this.state.record.CATEGORIES) {
      const id_category = this.state.record.CATEGORIES[i].id_category;
      const category = this.state.categories[id_category];
      categories.push(
        <button
          className="btn btn-transparent btn-small mr-1"
          style={{ borderColor: category.color ?? '' }}
        >
          <span
            className="text"
            style={{ color: category.color ?? '' }}
          >{category.category ?? ''}</span>
        </button>
      );
    }
    return <>
      <h2>{this.state.record.name ?? '[no-name]'}</h2>
      <small>Company {categories}</small>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <>
      <div className="grid grid-cols-2 gap-1">
        <div>
          {this.inputWrapper('name')}
          <FormInput title='Main contact'>
            <InputVarchar {...this.getDefaultInputProps()}
              value={R.MAIN_PERSON?.first_name ?? ''}
              placeholder={globalThis.app.translate('First name')}
              onChange={(value: any) => { this.updateRecord({MAIN_PERSON: {first_name: value}}); }}
            ></InputVarchar>
            <InputVarchar {...this.getDefaultInputProps()}
              value={R.MAIN_PERSON?.last_name ?? ''}
              placeholder={globalThis.app.translate('Last name')}
              onChange={(value: any) => { this.updateRecord({MAIN_PERSON: {last_name: value}}); }}
            ></InputVarchar>
          </FormInput>

          <FormInput title='Categories'>
            <InputTags2 {...this.getDefaultInputProps()}
              value={this.state.record.CATEGORIES}
              model='CeremonyCrmApp/Modules/Core/Sandbox/Models/Category'
              targetColumn='id_company'
              sourceColumn='id_category'
              colorColumn='color'
              onChange={(value: any) => {
                this.updateRecord({CATEGORIES: value});
              }}
            ></InputTags2>
          </FormInput>

          <div className="card mt-4">
            <div className="card-header">
              Other contacts - InputTable
              {this.state.isInlineEditing ?
                <button
                  className="btn btn-transparent"
                  onClick={() => {
                    if (!R.OTHER_PERSONS) R.OTHER_PERSONS = [];
                    R.OTHER_PERSONS.push({id_company: {_useMasterRecordId_: true}, is_main: false});
                    this.setState({record: R});
                  }}
                >
                  <span className="icon"><i className="fas fa-plus"></i></span>
                  <span className="text">Add contact</span>
                </button>
              : null}
            </div>
            <div className="card-body">
              <InputTable {...this.getDefaultInputProps()}
                model='CeremonyCrmApp/Modules/Core/Sandbox/Models/Person'
                value={R.OTHER_PERSONS}
                onChange={(value: any) => {
                  this.updateRecord({OTHER_PERSONS: value});
                }}
                columns={{
                  'first_name': {type: 'varchar', title: 'First name'},
                  'last_name': {type: 'varchar', title: 'Last name'},
                }}
              ></InputTable>
            </div>
          </div>
          <div className="card mt-4">
            <div className="card-header">
              Other contacts - FormInput with InputVarchar
            </div>
            <div className="card-body">
              {R.OTHER_PERSONS ? R.OTHER_PERSONS.map((item: any, key: number) => {
                return <>
                  <FormInput>
                    <div className="flex">
                      <InputVarchar {...this.getDefaultInputProps()}
                        value={item.first_name ?? ''}
                        placeholder={globalThis.app.translate('First name')}
                        onChange={(value: any) => {
                          this.updateRecord({OTHER_PERSONS: { [key] : {first_name: value} }});
                        }}
                      ></InputVarchar>
                      <InputVarchar {...this.getDefaultInputProps()}
                        value={item.last_name ?? ''}
                        placeholder={globalThis.app.translate('Last name')}
                        onChange={(value: any) => {
                          this.updateRecord({OTHER_PERSONS: { [key] : {last_name: value} }});
                        }}
                      ></InputVarchar>
                    </div>
                  </FormInput>
                </>;
              }) : globalThis.app.translate('No other contacts')}
            </div>
          </div>
        </div>
        <div>
          <div className="card">
            <div className="card-header">
              this.state.record
            </div>
            <div className="card-body">
              <pre
                style={{
                  color: 'blue',
                  width: '100%',
                  fontFamily: 'Courier New',
                  fontSize: '10px',
                }}
              >{JSON.stringify(R, null, 2)}</pre>
            </div>
          </div>
        </div>
      </div>
    </>;
  }
}
