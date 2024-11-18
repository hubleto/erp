import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormPerson from './FormPerson';
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
    itemsPerPage: 20,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Person',
    // className: 'header-style-1',
  }

  props: TablePersonsProps;

  getFormModalProps() {
    if (getUrlParam('recordId') > 0) {
      return {
        ...super.getFormModalProps(),
        type: 'right wide'
      }
    } else return {...super.getFormModalProps()}
  }

  // getFormProps(): any {
  //   return {
  //     ...super.getFormProps(),
  //     isInlineEditing: false,
  //   }
  // }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (data.CONTACTS && data.CONTACTS.length > 0) {
      if (columnName == "virt_email") {
        return (
          <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
            {data.CONTACTS.map((contact, key) => {
              if (contact.type == "email") {
                return (
                  <div className='border border-gray-400 rounded px-1'>
                    {contact.value} ({contact.CONTACT_TYPE.name})
                  </div>
                );
              } else return <></>;
            })}
          </div>
        );
      } else if (columnName == "virt_number") {
        return (
          <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
            {data.CONTACTS.map((contact, key) => {
              if (contact.type == "number") {
                return (
                  <div className='border border-gray-400 rounded px-1'>
                    {contact.value}  ({contact.CONTACT_TYPE.name})
                  </div>
                );
              } else return <></>;
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