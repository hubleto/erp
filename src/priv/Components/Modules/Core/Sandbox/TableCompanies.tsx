import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormCompany from "./FormCompany"

interface TableCompaniesProps extends TableProps {
}

interface TableCompaniesState extends TableState {
}

export default class TableCompanies extends Table<TableCompaniesProps, TableCompaniesState> {
  static defaultProps = {
    itemsPerPage: 100,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Core/Sandbox/Models/Company',
  }

  props: TableCompaniesProps;

  renderForm(): JSX.Element {
    return <FormCompany {...this.getFormParams()}/>;
  }
}