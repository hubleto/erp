import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormLead from './FormLead';
import InputTags2 from 'adios/Inputs/Tags2';

interface TableLeadsProps extends TableProps {
}

interface TableLeadsState extends TableState {
}

export default class TableLeads extends Table<TableLeadsProps, TableLeadsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/Lead',
  }

  props: TableLeadsProps;
  state: TableLeadsState;

  constructor(props: TableLeadsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableLeadsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderCell(columnName: string, column: any, data: any, options: any) {

    if (columnName == "id_lead_status") {
      if ( data.STATUS && data.STATUS.color) {
        return (
          <div className='flex flex-row '>
            <div style={{color: data.STATUS.color, borderColor: data.STATUS.color}} className='border rounded px-1'>{data.STATUS.name}</div>
          </div>
        )
      }
    } else if (columnName == "labels") {
      return (
        <div className='flex flex-row gap-2'>
          {data.LABELS.map((label, key) => {
            return (
              <div
                style={{color: label.LABEL.color, borderColor: label.LABEL.color}}
                className='border rounded px-1'>
                {label.LABEL.name}
              </div>
            );
          })}
        </div>
      );
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormLead {...formDescription}/>;
  }
}