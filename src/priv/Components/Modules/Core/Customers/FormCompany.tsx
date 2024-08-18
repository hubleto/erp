import React, { Component } from 'react'
import { deepObjectMerge } from "adios/Helper";
import Form, { FormProps, FormState } from 'adios/Form';
import InputVarchar from "adios/Inputs/Varchar";
import InputTags2 from "adios/Inputs/Tags2";
import InputTable from "adios/Inputs/Table";
import FormInput from "adios/FormInput";
import { Column } from 'primereact/column';

interface FormCompanyProps extends FormProps {
}

interface FormCompanyState extends FormState {
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
    }
  }

  normalizeRecord(record) {
    if (record.PERSONS) record.PERSONS.map((item: any, key: number) => {
      record.PERSONS[key].id_company = {_useMasterRecordId_: true};
    });
    if (record.BUSINESS_ACCOUNT) {
      record.BUSINESS_ACCOUNT.id_company = {_useMasterRecordId_: true};
    };

    return record;
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.name ?? '[no-name]'}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <>
      <div className="grid grid-cols-2 gap-1">
        <div>
          {this.inputWrapper('name')}
          {this.inputWrapper('id_account')}
          {this.inputWrapper('street')}
          {this.inputWrapper('city')}
          {this.inputWrapper('country')}
          {this.inputWrapper('postal_code')}

          <div className="card mt-4">
            <div className="card-header">
              Contacts
            </div>
            <div className="card-body">
              <InputTable {...this.getDefaultInputProps()}
                model='CeremonyCrmApp/Modules/Core/Customers/Models/Person'
                value={R.PERSONS}
                onChange={(value: any) => {
                  this.updateRecord({PERSONS: value});
                }}
                columns={{
                  'first_name': {type: 'varchar', title: 'First name'},
                  'last_name': {type: 'varchar', title: 'Last name'},
                }}
              ></InputTable>
              {this.state.isInlineEditing ?
                <a
                  role="button"
                  onClick={() => {
                    if (!R.PERSONS) R.PERSONS = [];
                    R.PERSONS.push({id_company: {_useMasterRecordId_: true}});
                    this.setState({record: R});
                  }}
                >
                  + Add contact
                </a>
              : null}
            </div>
          </div>
          <div className="card mt-4">
            <div className="card-header">
              Contacts - FormInput with InputVarchar
            </div>
            <div className="card-body">
              {R.PERSONS ? R.PERSONS.map((item: any, key: number) => {
                return <>
                  <FormInput>
                    <div className="flex">
                      <InputVarchar {...this.getDefaultInputProps()}
                        value={item.first_name ?? ''}
                        placeholder={globalThis.app.translate('First name')}
                        onChange={(value: any) => {
                          this.updateRecord({PERSONS: { [key] : {first_name: value} }});
                        }}
                      ></InputVarchar>
                      <InputVarchar {...this.getDefaultInputProps()}
                        value={item.last_name ?? ''}
                        placeholder={globalThis.app.translate('Last name')}
                        onChange={(value: any) => {
                          this.updateRecord({PERSONS: { [key] : {last_name: value} }});
                        }}
                      ></InputVarchar>
                    </div>
                  </FormInput>
                </>;
              }) : globalThis.app.translate('No contacts')}
            </div>
          </div>
          <div className="card mt-4">
            <div className="card-header">
              Business Account
            </div>
            <div className="card-body">
              {R.BUSINESS_ACCOUNT ?
                <FormInput>
                  <div className="grid grid-cols-2 gap-4">
                    <label htmlFor="">Vat ID</label>
                    <InputVarchar {...this.getDefaultInputProps()}
                      value={R.BUSINESS_ACCOUNT.vat_id ?? ''}
                      placeholder={globalThis.app.translate('VAT ID')}
                      onChange={(value: any) => {
                        this.updateRecord({BUSINESS_ACCOUNT: { vat_id: value } });
                      }}
                    ></InputVarchar>

                    <label htmlFor="">Company ID</label>
                    <InputVarchar {...this.getDefaultInputProps()}
                      value={R.BUSINESS_ACCOUNT.company_id ?? ''}
                      placeholder={globalThis.app.translate('Company ID')}
                      onChange={(value: any) => {
                        this.updateRecord({BUSINESS_ACCOUNT: { company_id: value } });
                      }}
                    ></InputVarchar>

                    <label htmlFor="">Tax ID</label>
                    <InputVarchar {...this.getDefaultInputProps()}
                      value={R.BUSINESS_ACCOUNT.tax_id ?? ''}
                      placeholder={globalThis.app.translate('Tax ID')}
                      onChange={(value: any) => {
                        this.updateRecord({BUSINESS_ACCOUNT: { tax_id: value } });
                      }}
                    ></InputVarchar>
                  </div>
                </FormInput>
                :
                <a
                  role="button"
                  onClick={() => {
                    if (!R.BUSINESS_ACCOUNT) R.BUSINESS_ACCOUNT = [];
                    R.BUSINESS_ACCOUNT.push({id_company: {_useMasterRecordId_: true}});
                    this.setState({record: R});
                  }}
                >
                  + Add Business Account
                </a>
              }
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
