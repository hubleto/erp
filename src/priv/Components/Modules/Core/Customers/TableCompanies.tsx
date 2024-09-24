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
    ...Table.defaultProps,
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