import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import TableContacts from './TableContacts';
import Lookup from 'adios/Inputs/Lookup';

export interface FormPersonProps extends HubletoFormProps {
  newEntryId?: number,
  creatingNew: boolean,
  tableContactsDescription: any
}

export interface FormPersonState extends HubletoFormState {
  newEntryId?: number,
}

export default class FormPerson<P, S> extends HubletoForm<FormPersonProps,FormPersonState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Contacts/Models/Person',
  };

  props: FormPersonProps;
  state: FormPersonState;

  translationContext: string = 'HubletoApp\\Community\\Contacts\\Loader::Components\\FormPerson';

  constructor(props: FormPersonProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
    }
  }

  getStateFromProps(props: FormPersonProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <h2>{'New Contact'}</h2>
      );
    } else {
      return (
        <h2>
          {this.state.record.first_name && this.state.record.last_name
            ? this.state.record.first_name + " " + this.state.record.last_name
            : "[Undefined Contact Name]"}
        </h2>
      );
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='grid grid-cols-1 gap-1' style=
          {{gridTemplateAreas:`
            'person'
            'contacts'
          `}}>
            <div className='card mt-4' style={{gridArea: 'person'}}>
              <div className='card-header'>{this.translate('Personal Information')}</div>
              <div className='card-body flex flex-row gap-2'>
                <div className="w-1/2">
                  {this.inputWrapper('first_name')}
                  {this.inputWrapper('last_name')}
                  <FormInput title={this.translate("Customer")}>
                    <Lookup {...this.getInputProps('id_customer')}
                      model='HubletoApp/Community/Contacts/Models/Customer'
                      endpoint={`customers/get-customer`}
                      value={R.id_customer}
                      readonly={this.props.creatingNew}
                      onChange={(value: any) => {
                        this.updateRecord({ id_customer: value});
                      }}
                    ></Lookup>
                  </FormInput>
                  <FormInput title={this.translate('Tags')}>
                    <InputTags2 {...this.getInputProps('id_person')}
                      value={this.state.record.TAGS}
                      model='HubletoApp/Community/Settings/Models/Tag'
                      targetColumn='id_person'
                      sourceColumn='id_tag'
                      colorColumn='color'
                      onChange={(value: any) => {
                        this.updateRecord({TAGS: value});
                      }}
                    ></InputTags2>
                  </FormInput>
                </div>
                <div className='border-l border-gray-200'></div>
                <div className="w-1/2">
                  {this.inputWrapper('note')}
                  {this.inputWrapper('is_active')}
                  {showAdditional ? this.inputWrapper('date_created') : null}
                </div>
              </div>
            </div>

            <div className='card mt-4' style={{gridArea: 'contacts'}}>
              <div className='card-header'>Contacts</div>
              <div className='card-body'>
                {this.state.isInlineEditing ? (
                  <a
                    className="btn btn-add mb-2"
                    onClick={() => {
                      if (!R.CONTACTS) R.CONTACTS = [];
                      R.CONTACTS.push({
                        id: this.state.newEntryId,
                        id_person: { _useMasterRecordId_: true },
                        type: 'email',
                      });
                      this.updateRecord({ CONTACTS: R.CONTACTS });
                      this.setState({ newEntryId: this.state.newEntryId - 1 } as FormPersonState);
                    }}
                  >
                    <span className="icon"><i className="fas fa-add"></i></span>
                    <span className="text">{this.translate('Add contact')}</span>
                  </a>
                ) : null}
                <TableContacts
                  uid={this.props.uid + '_table_contacts'}
                  context="Hello World"
                  data={{data: R.CONTACTS}}
                  isInlineEditing={this.state.isInlineEditing}
                  isUsedAsInput={true}
                  readonly={!this.state.isInlineEditing}
                  descriptionSource="props"
                  onRowClick={() => this.setState({isInlineEditing: true})}
                  onChange={(table: TableContacts) => {
                    this.updateRecord({ CONTACTS: table.state.data.data });
                  }}
                  onDeleteSelectionChange={(table: TableContacts) => {
                    this.updateRecord({ CONTACTS: table.state.data.data ?? [] });
                  }}
                  customEndpointParams={{idPerson: R.id}}
                  description={{
                    permissions: this.props.tableContactsDescription.permissions,
                    ui: {
                      emptyMessage: <div className="p-2">{this.translate('No contacts for this person yet.')}</div>
                    },
                    columns: {
                      type: {
                        type: 'varchar',
                        title: this.translate('Type'),
                        enumValues: {'email' : this.translate('Email'), 'number': this.translate('Phone Number'), 'other': this.translate('Other')},
                      },
                      value: { type: 'varchar', title: this.translate('Value')},
                      id_contact_category: { type: 'lookup', title: this.translate('Category'), model: 'HubletoApp/Community/Contacts/Models/ContactCategory' },
                    },
                    inputs: {
                      type: {
                        type: 'varchar',
                        title: this.translate('Type'),
                        enumValues: {'email' : 'Email', 'number' : 'Phone Number', 'other': 'Other'},
                      },
                      value: { type: 'varchar', title: this.translate('Value')},
                      id_contact_category: { type: 'lookup', title: this.translate('Category'), model: 'HubletoApp/Community/Contacts/Models/ContactCategory' },
                    }
                  }}
                />
              </div>
            </div>
        </div>
      </>
    );
  }
}
