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
    itemsPerPage: 15,
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

  loadTableDescription(successCallback?: (params: any) => void): void {
    {
      this.props.description
      ??
      this.setState({
        description: {
          ui: {
            title: 'Contact Persons',
            showHeader: true,
            showFooter: false,
            addButtonText: 'Add Contact Person',
          },
          permissions: {
            canCreate: true,
            canDelete: true,
            canRead: true,
            canUpdate: true,
          },
          columns: {
            first_name: { type: 'varchar', title: 'First Name' },
            last_name: { type: 'varchar', title: 'Last Name'},
            id_company: { type: 'lookup', title: 'Company', model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Company' },
            virt_address: { type: 'varchar', title: 'Main Address',},
            virt_email: { type: 'varchar', title: 'Main Email',},
            virt_number: { type: 'varchar', title: 'Main Phone Number',},
            is_active: { type: 'boolean', title: 'Active',},
          }
        }
      });
    }
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormPerson {...formProps}/>;
  }
}