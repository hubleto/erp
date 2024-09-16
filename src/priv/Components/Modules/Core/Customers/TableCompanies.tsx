import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormCompany from './FormCompany';
import { getUrlParam } from 'adios/Helper';
import { FormProps } from 'adios/Form';

interface TableCompaniesProps extends TableProps {
}

interface TableCompaniesState extends TableState {
}

export default class TableCompanies extends Table<TableCompaniesProps, TableCompaniesState> {
  static defaultProps = {
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
  }

  props: TableCompaniesProps;
  state: TableCompaniesState;

  getFormModalProps() {
    if (getUrlParam('recordId') > 0) {
      return {
        ...super.getFormModalProps(),
        type: 'right wide'
      }
    } else return {...super.getFormModalProps()}
  }

  /* getFormProps(): any {
    return {
      ...super.getFormProps(),
    }
  } */

  /* loadTableDescription(successCallback?: (params: any) => void): void {
    this.setState({
      description: {
        ui: {
          title: 'Companies',
          addButtonText: 'Add Companies',
          showHeader: true,
          showFooter: false,
          showFilter: false,
        },
        columns: {
          name: { type: 'varchar', title: 'Company Name' },
          street_line_1: { type: 'varchar', title: 'Street Line 1'},
          street_line_2: { type: 'varchar', title: 'Street Line 2'},
          region: { type: 'varchar', title: 'Region'},
          city: { type: 'varchar', title: 'City'},
          id_country: { type: 'lookup', title: 'Country', model: 'CeremonyCrmApp/Modules/Core/Settings/Models/Country'},
          postal_code: { type: 'varchar', title: 'Postal Code'},
          vat_id: { type: 'varchar', title: 'Vat ID'},
          company_id: { type: 'varchar', title: 'Company ID'},
          tax_id: { type: 'varchar', title: 'Tax ID'},
          note: { type: 'Text', title: 'Notes'},
          is_active: { type: 'boolean', title: 'Active'},
        },
        permissions:{
          canCreate: true,
          canDelete: true,
          canRead: true,
          canUpdate: true
        },
      }
    })
  } */

  /*

  constructor(props: TableCompaniesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  } */

 /*  getFormModalProps(): any {
    let params: any = super.getFormModalProps();
    params.type = this.state.formId == -1 ? 'centered' : 'right wide';
    return params;
  }
  getStateFromProps(props: TableCompaniesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  } */

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormCompany {...formProps}/>;
  }
}