import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormPerson, { FormPersonProps, FormPersonState } from './FormPerson';
import { getUrlParam } from 'adios/Helper';
import { FormProps } from 'adios/Form';

interface TablePersonsProps extends TableProps {
  showHeader: boolean,
  showFooter: boolean
}

interface TablePersonsState extends TableState {
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
    model: 'CeremonyCrmMod/Core/Customers/Models/Person',
    // className: 'header-style-1',
  }

  props: TablePersonsProps;

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

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (data.CONTACTS && data.CONTACTS.length > 0) {
      if (columnName == "virt_email") {
        let contactsRendered = 0;
        return (
          <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
            {data.CONTACTS.map((contact, key) => {
              if (contact.type == "email" && contactsRendered < 2) {
                contactsRendered += 1;
                return (
                  <div className='border border-gray-400 rounded px-1'>
                    {contact.value} ({contact.CONTACT_TYPE.name})
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
                  <div className='border border-gray-400 rounded px-1'>
                    {contact.value}  ({contact.CONTACT_TYPE.name})
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
    let formProps: FormProps = this.getFormProps();
    return <FormPerson {...formProps}/>;
  }
}