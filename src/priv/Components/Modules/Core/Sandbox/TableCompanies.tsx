import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormCompany from "./FormCompany"

interface TableCompaniesProps extends TableProps {
  categories?: Array<any>,
}

interface TableCompaniesState extends TableState {
  categories: Array<any>,
}

export default class TableCompanies extends Table<TableCompaniesProps, TableCompaniesState> {
  static defaultProps = {
    itemsPerPage: 100,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Core/Sandbox/Models/Company',
  }

  props: TableCompaniesProps;
  state: TableCompaniesState;

  constructor(props: TableCompaniesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCompaniesProps) {
    return {
      ...super.getStateFromProps(props),
      categories: props.categories ?? [],
    }
  }

  renderForm(): JSX.Element {
    let formParams = this.getFormParams();
    formParams.categories = this.state.categories;
    return <FormCompany {...formParams}/>;
  }
}