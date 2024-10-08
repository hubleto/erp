import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';
import TableAddresses from './TableAddresses';
import TableContacts from './TableContacts';

interface FormPersonProps extends FormProps {}

interface FormPersonState extends FormState {}

export default class FormPerson<P, S> extends Form<FormPersonProps,FormPersonState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Person',
  };

  props: FormPersonProps;
  state: FormPersonState;

  constructor(props: FormPersonProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormPersonProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  normalizeRecord(record) {
    if (record.ADDRESSES) record.ADDRESSES.map((item: any, key: number) => {
      record.ADDRESSES[key].id_person = {_useMasterRecordId_: true};
    });
    if (record.CONTACTS) record.CONTACTS.map((item: any, key: number) => {
      record.CONTACTS[key].id_person = {_useMasterRecordId_: true};
    });
    if (record.TAGS) record.TAGS.map((item: any, key: number) => {
      record.TAGS[key].id_person = {_useMasterRecordId_: true};
    });

    return record;
  }

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New Person'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.last_name
              ? this.state.record.first_name + ' ' + this.state.record.last_name
              : '[Undefined Name]'}
          </h2>
        </>
      );
    }
  }

  onBeforeSaveRecord(record: any) {
    if (!record.is_primary) {
      record.is_primary = 0;
    }

    return record;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='grid grid-cols-2 gap-1' style=
          {{gridTemplateAreas:`
            'person contacts'
            'addresses addresses'
          `}}>
            <div className='card mt-4' style={{gridArea: 'person'}}>
              <div className='card-header'>Personal Information</div>
              <div className='card-body'>
                {this.inputWrapper('first_name')}
                {this.inputWrapper('last_name')}
                {this.inputWrapper('id_company')}
                {this.inputWrapper('is_primary')}
                {showAdditional ? this.inputWrapper('is_active') : null}
                <FormInput title='Categories'>
                  <InputTags2 {...this.getDefaultInputProps()}
                    value={this.state.record.TAGS}
                    model='CeremonyCrmApp/Modules/Core/Settings/Models/Tag'
                    targetColumn='id_person'
                    sourceColumn='id_tag'
                    colorColumn='color'
                    onChange={(value: any) => {
                      this.updateRecord({TAGS: value});
                    }}
                  ></InputTags2>
                </FormInput>
              </div>
            </div>

            <div className='card mt-4' style={{gridArea: 'addresses'}}>
              <div className='card-header'>Addresses</div>
              <div className='card-body'>
                <InputTable
                  uid={this.props.uid + '_table_addresses_input'}
                  {...this.getDefaultInputProps()}
                  value={R.ADDRESSES}
                  onChange={(value: any) => {
                    this.updateRecord({ ADDRESSES: value });
                  }}
                >
                  <TableAddresses
                    uid={this.props.uid + '_table_addresses'}
                    context="Hello World"
                    descriptionSource="props"
                    description={{
                      permissions: {
                        canCreate: true,
                        canDelete: true,
                        canRead: true,
                        canUpdate: true
                      },
                      columns: {
                        street_line_1: { type: 'varchar', title: 'Street Line 1' },
                        street_line_2: { type: 'varchar', title: 'Street Line 2' },
                        city: { type: 'varchar', title: 'City' },
                        region: { type: 'varchar', title: 'Region' },
                        postal_code: { type: 'varchar', title: 'Postal Code' },
                        id_country: { type: 'lookup', model: 'CeremonyCrmApp/Modules/Core/Settings/Models/Country', title: 'Country' },
                      }
                    }}
                  ></TableAddresses>
                </InputTable>
                {this.state.isInlineEditing ? (
                  <a
                    role='button'
                    onClick={() => {
                      if (!R.ADDRESSES) R.ADDRESSES = [];
                      R.ADDRESSES.push({
                        id_person: { _useMasterRecordId_: true },
                      });
                      this.setState({ record: R });
                    }}
                  >
                    + Add address
                  </a>
                ) : null}
              </div>
            </div>

            <div className='card mt-4' style={{gridArea: 'contacts'}}>
              <div className='card-header'>Contacts</div>
              <div className='card-body'>
                <InputTable
                  uid={this.props.uid + '_table_contacts_input'}
                  {...this.getDefaultInputProps()}
                  value={R.CONTACTS}
                  onChange={(value: any) => {
                    this.updateRecord({ CONTACTS: value });
                  }}
                >
                  <TableContacts
                    uid={this.props.uid + '_table_contacts'}
                    context="Hello World"
                    descriptionSource="props"
                    description={{
                      permissions: {
                        canCreate: true,
                        canDelete: true,
                        canRead: true,
                        canUpdate: true
                      },
                      columns: {
                        type: {
                          type: 'varchar',
                          title: 'Contact type',
                          enumValues: {'email' : 'Email', 'number' : 'Phone Number'},
                          //enumCssClasses: {'email' : 'bg-yellow-200', 'number' : 'bg-blue-200'},
                        },
                        value: { type: 'varchar', title: 'Value' },
                      }
                    }}
                  ></TableContacts>
                </InputTable>
                {this.state.isInlineEditing ? (
                  <a
                    role='button'
                    onClick={() => {
                      if (!R.CONTACTS) R.CONTACTS = [];
                      R.CONTACTS.push({
                        id_person: { _useMasterRecordId_: true },
                        type: 'email',
                      });
                      this.setState({ record: R });
                    }}
                  >
                    + Add Contact
                  </a>
                ) : null}
              </div>
            </div>
        </div>
      </>
    );
  }
}
