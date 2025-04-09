import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormPerson, { FormPersonProps, FormPersonState } from './FormPerson';
import { getUrlParam } from 'adios/Helper';
import request from 'adios/Request';

interface TablePersonsProps extends TableProps {}

interface TablePersonsState extends TableState {
  tableContactsDescription?: any,
}

export default class TablePersons extends Table<TablePersonsProps, TablePersonsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Contacts/Models/Person',
  }

  props: TablePersonsProps;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\TablePersons';

  getFormModalProps() {
    if (getUrlParam('recordId') > 0) {
      return {
        ...super.getFormModalProps(),
        type: 'right'
      }
    } else return {...super.getFormModalProps()}
  }

  getFormProps(): any {
    var formProps = super.getFormProps();
    return {
      ...super.getFormProps(),
      onSaveCallback: (form: FormPerson<FormPersonProps, FormPersonState>, saveResponse: any) => {
        formProps.onSaveCallback(form, saveResponse);
        if (this.props.parentForm) {
          this.props.parentForm.reload();
        }
      },
      onDeleteCallback: (form: FormPerson<FormPersonProps, FormPersonState>, saveResponse: any) => {
        formProps.onDeleteCallback(form, saveResponse);
        if (this.props.parentForm) {
          this.props.parentForm.reload();
        }
      }
    }
  }

  onAfterLoadTableDescription(description: any) {
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Contacts/Models/Contact',
        idPerson: this.props.recordId ?? description.idPerson,
      },
      (description: any) => {
        this.setState({tableContactsDescription: description} as TablePersonsState);
      }
    );
    return description;
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "tags") {
      return (
        <>
          {data.TAGS.map((tag, key) => {
            return <div style={{backgroundColor: tag.TAG.color}} className='badge'>{tag.TAG.name}</div>;
          })}
        </>
      );
    } else if (data.CONTACTS && data.CONTACTS.length > 0) {
      if (columnName == "virt_email") {
        let contactsRendered = 0;
        return (
          <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
            {data.CONTACTS.map((contact, key) => {
              if (contact.type == "email" && contactsRendered < 2) {
                contactsRendered += 1;
                return (
                  <div className='border border-gray-400 rounded px-1' key={data.id + '-email-' + key}>
                    {contact.value} {contact.CONTACT_CATEGORY ? <>({contact.CONTACT_CATEGORY.name})</> : null}
                  </div>
                );
              } else return null;
            })}
          </div>
        );
      } else if (columnName == "virt_number") {
        let contactsRendered = 0;
        return (
          <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
            {data.CONTACTS.map((contact, key) => {
              if (contact.type == "number" && contactsRendered < 2) {
                contactsRendered += 1;
                return (
                  <div className='border border-gray-400 rounded px-1' key={data.id + '-number-' + key}>
                    {contact.value} {contact.CONTACT_CATEGORY ? <>({contact.CONTACT_CATEGORY.name})</> : null}
                  </div>
                );
              } else return null;
            })}
          </div>
        );
      } else return super.renderCell(columnName, column, data, options);
    } else return super.renderCell(columnName, column, data, options);
  }

  renderForm(): JSX.Element {
    let formProps: FormPersonProps = this.getFormProps();
    formProps.tableContactsDescription = this.state.tableContactsDescription as TablePersonsState;
    return <FormPerson {...formProps}/>;
  }
}