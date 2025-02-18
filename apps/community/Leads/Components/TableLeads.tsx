import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormLead from './FormLead';
import InputTags2 from 'adios/Inputs/Tags2';

interface TableLeadsProps extends TableProps {
  showArchive?: boolean,
}

interface TableLeadsState extends TableState {
  showArchive: boolean,
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
    model: 'HubletoApp/Community/Leads/Models/Lead',
  }

  props: TableLeadsProps;
  state: TableLeadsState;

  translationContext: string = 'HubletoApp/Community/Leads/Components/TableLeads';

  constructor(props: TableLeadsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableLeadsProps) {
    return {
      ...super.getStateFromProps(props),
      showArchive: props.showArchive ?? false,
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      showArchive: this.state.showArchive ? 1 : 0,
    }
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    if (!this.state.showArchive) {
      elements.push(
        <a className="btn btn-transparent" href="leads/archive">
          <span className="icon"><i className="fas fa-box-archive"></i></span>
          <span className="text">Archive</span>
        </a>
      );
    }

    return elements;
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "id_lead_status") {
      if (data.STATUS && data.STATUS.color) {
        return <div style={{backgroundColor: data.STATUS.color}} className='badge'>{data.STATUS.name}</div>;
      }
    } else if (columnName == "tags") {
      return (
        <>
          {data.TAGS.map((tag, key) => {
            return <div style={{backgroundColor: tag.TAG.color}} className='badge'>{tag.TAG.name}</div>;
          })}
        </>
      );
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormLead {...formProps}/>;
  }
}