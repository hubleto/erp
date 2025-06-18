import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormLead, { FormLeadProps } from './FormLead';
import request from 'adios/Request';
import { collapseTextChangeRangesAcrossMultipleVersions } from 'typescript';

export interface TableLeadsProps extends TableProps {
  idCustomer?: number,
}

export interface TableLeadsState extends TableState {
  fArchive: number,
  fStatus: number,
  fOwnership: number,
}

export default class TableLeads extends Table<TableLeadsProps, TableLeadsState> {
  static defaultProps = {
    ...Table.defaultProps,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Leads/Models/Lead',
  }

  props: TableLeadsProps;
  state: TableLeadsState;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\TableLeads';

  constructor(props: TableLeadsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableLeadsProps) {
    return {
      ...super.getStateFromProps(props),
      fArchive: 0,
      fStatus: 0,
      fOwnership: 0,
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
      idCustomer: this.props.idCustomer,
      fStatus: this.state.fStatus,
      fOwnership: this.state.fOwnership,
      fArchive: this.state.fArchive,
    }
  }

  renderSidebarFilter(): JSX.Element {
    const fStatusOptions = {0: 'All', 1: 'New', 2: 'In progress', 3: 'Completed', 4: 'Lost'};
    const fOwnershipOptions = {0: 'All', 1: 'Owned by me', 2: 'Managed by me'};
    const fArchiveOptions = {0: 'Active', 1: 'Archived'};

    return <div className="flex flex-col gap-2 text-nowrap">
      <b>Status</b>
      <div className="list">
        {Object.keys(fStatusOptions).map((key: any) => {
          return <button
            className={"btn btn-small btn-list-item " + (this.state.fStatus == key ? "btn-primary" : "btn-transparent")}
            onClick={() => this.setState({fStatus: key}, () => this.loadData())}
          ><span className="text">{fStatusOptions[key]}</span></button>;
        })}
      </div>
      <b>Ownership</b>
      <div className="list">
        {Object.keys(fOwnershipOptions).map((key: any) => {
          return <button
            className={"btn btn-small btn-list-item " + (this.state.fOwnership == key ? "btn-primary" : "btn-transparent")}
            onClick={() => this.setState({fOwnership: key}, () => this.loadData())}
          ><span className="text">{fOwnershipOptions[key]}</span></button>;
        })}
      </div>
      <b>Archive</b>
      <div className="list">
        {Object.keys(fArchiveOptions).map((key: any) => {
          return <button
            className={"btn btn-small btn-list-item " + (this.state.fArchive == key ? "btn-primary" : "btn-transparent")}
            onClick={() => this.setState({fArchive: key}, () => this.loadData())}
          ><span className="text">{fArchiveOptions[key]}</span></button>;
        })}
      </div>
    </div>;
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "tags") {
      return (
        <>
          {data.TAGS.map((tag, key) => {
            return <div style={{backgroundColor: tag.TAG.color}} className='badge' key={data.id + '-tags-' + key}>{tag.TAG.name}</div>;
          })}
        </>
      );
    } else if (columnName == "DEAL") {
      if (data.DEAL) {
        return <>
          <a
            className="btn btn-transparent btn-small"
            href={"deals/" + data.DEAL.id}
            target="_blank"
          >
            <span className="icon"><i className="fas fa-arrow-right"></i></span>
            <span className="text">{data.DEAL.identifier}</span>
          </a>
        </>
      } else {
        return null;
      }
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  onAfterLoadTableDescription(description: any) {
    description.columns['DEAL'] = {
      type: 'varchar',
      title: globalThis.main.translate('Deal'),
    };

    return description;
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormLeadProps;
    formProps.customEndpointParams.idCustomer = this.props.idCustomer;
    // formProps.customEndpointParams.showArchive = this.props.showArchive ?? false;
    return <FormLead {...formProps}/>;
  }
}