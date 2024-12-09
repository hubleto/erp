import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormLead from './FormLead';
import InputTags2 from 'adios/Inputs/Tags2';

interface TableLeadsArchiveProps extends TableProps {
}

interface TableLeadsArchiveState extends TableState {
}

export default class TableLeadsArchive extends Table<TableLeadsArchiveProps, TableLeadsArchiveState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Sales/Leads/Models/Lead',
    endpoint: {
      describeTable:  'api/table/describe',
      getRecords: 'api/record/get-list?archive=1',
      deleteRecord:  'api/record/delete',
    },
   descriptionSource: 'both',
    description: {
      ui: {
        showHeader: true,
        showFooter: false,
        title: "Leads Archive"
      },
      permissions: {
        canCreate: false,
        canUpdate: false,
        canDelete: false,
        canRead: true,
      },
        /*columns: {
        title: { type: "varchar", title: "Title"},
        id_company: { type: "lookup", title: "Company", model: "CeremonyCrmApp/Modules/Core/Customers/Models/Company"},
        price: { type: "float", title: "Price"},
        id_currency: { type: "lookup", title: "Currency", model: "CeremonyCrmApp/Modules/Core/Settings/Models/Currency"},
        date_expected_close: { type: "date", title: "Expected Close Date"},
        id_user: { type: "lookup", title: "Title", model: "CeremonyCrmApp/Modules/Core/Settings/Models/User"},
        date_created: { type: "date", title: "Date Created"},
        id_lead_status: { type: "lookup", title: "Title", model: "CeremonyCrmApp/Modules/Core/Settings/Models/LeadStatus"},
        labels: { type: "none", title: "Labels"},
      }*/
    }
  }

  props: TableLeadsArchiveProps;
  state: TableLeadsArchiveState;

  constructor(props: TableLeadsArchiveProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableLeadsArchiveProps) {
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