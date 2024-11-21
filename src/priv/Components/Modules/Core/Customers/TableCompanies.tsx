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
        type: 'right'
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


  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "categories") {
      return (
        <div className='flex flex-row flex-wrap gap-2 min-w-64'>
          {data.TAGS.map((tag, key) => {
            return (
              <div
                style={{color: tag.TAG.color, borderColor: tag.TAG.color}}
                className='border rounded px-1'>
                {tag.TAG.name}
              </div>
            );
          })}
        </div>
      );
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  render(): JSX.Element {
    return <>
    {this.state.tableOptions == true ?
    <>
      {this.renderOptionsForm()}
      {super.render()}
    </>
    :
    super.render()
    }

    </>
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormCompany {...formProps}/>;
  }
}