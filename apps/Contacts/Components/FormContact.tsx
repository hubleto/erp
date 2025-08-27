import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import InputTags2 from '@hubleto/react-ui/core/Inputs/Tags2';
import FormInput from '@hubleto/react-ui/core/FormInput';
import TableValues from './TableValues';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import Boolean from '@hubleto/react-ui/core/Inputs/Boolean';
import request from '@hubleto/react-ui/core/Request';
import { FormProps, FormState } from '@hubleto/react-ui/core/Form';

export interface FormContactProps extends HubletoFormProps {
  newEntryId?: number,
  creatingNew: boolean,
  tableValuesDescription: any
}

export interface FormContactState extends HubletoFormState {
  newEntryId?: number,
  primaryContactMessage: boolean,
  contactsTableKey: number,
}

export default class FormContact<P, S> extends HubletoForm<FormContactProps,FormContactState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Contacts/Models/Contact',
  };

  props: FormContactProps;
  state: FormContactState;

  translationContext: string = 'HubletoApp\\Community\\Contacts\\Loader::Components\\FormContact';

  constructor(props: FormContactProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      primaryContactMessage: false,
      contactsTableKey: 0,
    }
  }

  getStateFromProps(props: FormContactProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Contact')}</small>
      <h2>{this.state.record.first_name ?? ''}&nbsp;{this.state.record.last_name ?? ''}</h2>
    </>;
  }

  checkPrimaryContact(R) {
    if (R.TAGS && R.TAGS.length > 0) {
      var tagIds = [];

      for (const [key, value] of Object.entries(R.TAGS)) {
          tagIds.push(value['id_tag'] ?? 0);
      }

      request.get(
        "contacts/check-primary-contact",
        {
          idContact: this.state.record.id ?? -1,
          idCustomer: R.id_customer,
          tags: tagIds
        },
        (response: any) => {
          if (response.result == true) {
            this.updateRecord({is_primary: 1})
          } else {
            globalThis.main.showDialogDanger(
              <>
                <p className='text'>{response.error ?? "Unknown error"}</p>
                <p className='text'>{response.names != null ? response.names : ""}</p>
              </>,
              {}
            )
          }
        }
      )
    } else {
      this.updateRecord({is_primary: 1});
    }
  }

  componentDidUpdate(prevProps: FormProps, prevState: FormState): void {
    if (prevState.isInlineEditing != this.state.isInlineEditing) this.setState({contactsTableKey: Math.random()} as FormContactState)
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;
    const customInputs = this.renderCustomInputs();

    return <>
      <div className='card'>
        <div className='card-body flex flex-row gap-2'>
          <div className="w-3/4">
            <div className="flex gap-2 w-full">
              <div>
                <i className="fas fa-user text-2xl p-4 text-gray-500"></i>
              </div>
              <div className="w-full">
                {this.inputWrapper('salutation')}
                <div className="flex gap-2">
                  <div className="flex-1">{this.inputWrapper('title_before', {cssClass: 'text-2xl'})}</div>
                  <div className="flex-2">{this.inputWrapper('first_name', {cssClass: 'text-2xl text-primary'})}</div>
                  <div className="flex-2">{this.inputWrapper('middle_name', {cssClass: 'text-2xl text-primary'})}</div>
                  <div className="flex-2">{this.inputWrapper('last_name', {cssClass: 'text-2xl text-primary'})}</div>
                  <div className="flex-1">{this.inputWrapper('title_after', {cssClass: 'text-2xl'})}</div>
                </div>
              </div>
            </div>

            {this.divider(this.translate('Contacts'))}
            <TableValues
              key={"contacts_"+this.state.contactsTableKey}
              uid={this.props.uid + '_table_contacts'}
              parentForm={this}
              context="Hello World"
              // customEndpointParams={{idContact: R.id}}
              data={{data: R.VALUES}}
              isInlineEditing={this.state.isInlineEditing}
              isUsedAsInput={true}
              readonly={!this.state.isInlineEditing}
              descriptionSource="props"
              onRowClick={() => this.setState({isInlineEditing: true})}
              onChange={(table: TableValues) => {
                this.updateRecord({ VALUES: table.state.data.data });
              }}
              onDeleteSelectionChange={(table: TableValues) => {
                this.updateRecord({ VALUES: table.state.data.data ?? [] });
                this.setState({contactsTableKey: Math.random()} as FormContactState)
              }}
              customEndpointParams={{idContact: R.id}}
              description={{
                permissions: this.props.tableValuesDescription?.permissions ?? {},
                ui: {
                  emptyMessage: <div className="p-2">{this.translate('No contacts yet.')}</div>
                },
                columns: {
                  type: {
                    type: 'varchar',
                    title: this.translate('Type'),
                    enumValues: {'email' : this.translate('Email'), 'number': this.translate('Phone Number'), 'other': this.translate('Other')},
                  },
                  value: { type: 'varchar', title: this.translate('Value')},
                  id_category: { type: 'lookup', title: this.translate('Category'), model: 'HubletoApp/Community/Contacts/Models/Category' },
                },
                inputs: {
                  type: {
                    type: 'varchar',
                    title: this.translate('Type'),
                    enumValues: {'email' : 'Email', 'number' : 'Phone Number', 'other': 'Other'},
                  },
                  value: { type: 'varchar', title: this.translate('Value')},
                  id_category: { type: 'lookup', title: this.translate('Category'), model: 'HubletoApp/Community/Contacts/Models/Category' },
                }
              }}
            />
            <a
              className="btn btn-add-outline mt-2"
              onClick={() => {
                if (!R.VALUES) R.VALUES = [];
                R.VALUES.push({
                  id: this.state.newEntryId,
                  id_contact: { _useMasterRecordId_: true },
                  type: 'email',
                });
                this.setState({ isInlineEditing: true, newEntryId: this.state.newEntryId - 1 } as FormContactState);
              }}
            >
              <span className="icon"><i className="fas fa-add"></i></span>
              <span className="text">{this.translate('Add contact')}</span>
            </a>

          </div>
          <div className='border-l border-gray-200'></div>
          <div className="w-1/2">
            <FormInput title={this.translate("Customer")}>
              <Lookup {...this.getInputProps('id_customer')}
                model='HubletoApp/Community/Contacts/Models/Customer'
                endpoint={`customers/api/get-customer`}
                value={R.id_customer}
                readonly={this.props.creatingNew}
                onChange={(input: any, value: any) => {
                  if (this.state.record.is_primary == 1) {
                    this.setState({primaryContactMessage: true} as FormContactState);
                  }
                  this.updateRecord({ id_customer: value, is_primary: 0});
                }}
              ></Lookup>
            </FormInput>
            <FormInput title={this.translate('Tags')}>
              <InputTags2 {...this.getInputProps('id_tag')}
                value={this.state.record.TAGS}
                model='HubletoApp/Community/Contacts/Models/Tag'
                targetColumn='id_contact'
                sourceColumn='id_tag'
                colorColumn='color'
                onChange={(input: any, value: any) => {
                  if (this.state.record.is_primary == 1) {
                    this.setState({primaryContactMessage: true} as FormContactState);
                  }
                  R.TAGS = value;
                  this.setState({record: R});
                  this.updateRecord({is_primary: 0});
                }}
              ></InputTags2>
            </FormInput>
            {this.state.record?.id_customer > 0 ?
              <>
                {this.state.primaryContactMessage == true ?
                  <div className='text-yellow-500 block'>
                    <span className='icon mr-2'><i className='fas fa-triangle-exclamation'></i></span>
                    <span className='text'>
                      {this.translate("Due to some changes the Primary Contact has to be checked again")}
                    </span>
                  </div>
                : <></>}
                <FormInput title={this.translate("Primary Contact")}>
                  <Boolean {...this.getInputProps("is_primary")}
                    value={this.state.record.is_primary}
                    onChange={(input: any, value: any) => {
                      this.setState({isInlineEditing: true});
                      if (value == 1) {
                        this.setState({primaryContactMessage: false} as FormContactState)
                        this.checkPrimaryContact(this.state.record);
                      } else this.updateRecord({is_primary: value})
                    }}
                  ></Boolean>
                </FormInput>

              </>
            : <></>}
            {this.inputWrapper('note', {cssClass: 'bg-yellow-50'})}
            {this.inputWrapper('is_valid')}
            {showAdditional ? this.inputWrapper('date_created') : null}
          </div>
        </div>
      </div>
      {showAdditional && customInputs.length > 0 ?
        <div className="card mt-2"><div className="card-header">{this.translate("Custom data")}</div><div className="card-body">
          {customInputs}
        </div></div>
      : <></>}
    </>;
  }
}
