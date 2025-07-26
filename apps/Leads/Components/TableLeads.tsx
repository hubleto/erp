import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormLead, { FormLeadProps } from './FormLead';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";
import request from '@hubleto/react-ui/core/Request';

export interface TableLeadsProps extends HubletoTableProps {
  idCustomer?: number,
  idCampaign?: number,
}

export interface TableLeadsState extends HubletoTableState {
  showSetStatusDialog: boolean,
  selectedBulkStatus: number,
  rerenderKey: number,
}

export default class TableLeads extends HubletoTable<TableLeadsProps, TableLeadsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
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
    this.state = {
      ...this.getStateFromProps(props),
      showSetStatusDialog: false,
      rerenderKey: Math.random(),
      selectedBulkStatus: 0,
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
      idCampaign: this.props.idCampaign,
    }
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
    formProps.customEndpointParams.idCampaign = this.props.idCampaign;
    return <FormLead {...formProps}/>;
  }

  saveBulkStatusChange() {
    request.post(
      "leads/save-bulk-status-change",
      {
        record: this.state.selection,
      },
      {},
      (data: any) => {
        if (data.status == "success") {
          this.setState({showSetStatusDialog: false, selection: null})
          this.loadData();
        }
      }
    );
  }

  bulkStatusChange(newStatus: any) {
    if (this.state.selection.length > 0) {
      let data = this.state.selection;
      Object.entries(data).map((item, index) => item[1].status = newStatus)
      this.setState({selection: data, rerenderKey: Math.random()});
    }
  }

  renderContent(): JSX.Element {

    let saveButton = <>
      <button className='btn btn-edit' onClick={() => this.saveBulkStatusChange()}>
        <span className="icon"><i className="fas fa-save"></i></span>
        <span className="text">
          Save
        </span>
      </button>
    </>

    return <>
      {super.renderContent()}
      {this.state.showSetStatusDialog ?
        <ModalSimple
          uid={this.props.uid + '_form_bulk_status_change'}
          isOpen={true}
          title={this.translate('Bulk change Lead status')}
          type='centered'
          showHeader={true}
          headerLeft={saveButton}
          onClose={() => { this.setState({showSetStatusDialog: false}); }}
        >
          <>
            <div className='w-[100%] flex flex-row justify-end items-center gap-2 mb-2'>
              <span className='text font-bold'>{this.translate("Bulk change status")}:</span>
              <select
                className='w-1/4 mr-[15px]'
                value={this.state.selectedBulkStatus}
                onChange={(e) => {
                  this.setState({selectedBulkStatus: e.target.value});
                  this.bulkStatusChange(e.target.value);
                }}
              >
                <option value={0}>{this.translate("No interaction yet")}</option>
                <option value={1}>{this.translate("Contacted")}</option>
                <option value={2}>{this.translate("In Progress")}</option>
                <option value={3}>{this.translate("Closed")}</option>
                <option value={20}>C{this.translate("onverted to deal")}</option>
              </select>
            </div>
            <HubletoTable
              key={this.state.rerenderKey}
              model={this.model}
              data={{data: this.state.selection}}
              readonly={true}
              uid={this.props.uid + 'table_leads_mass_status_change'}
              descriptionSource='props'
              isInlineEditing={true}
              onRowClick={() => null}
              onChange={(table: TableLeads) => {this.setState({selection: table.state.data?.data})}}
              description={{
                columns: {
                  identifier: {type: "varchar", title: this.translate("Identifier"), readonly: true,
                    cellRenderer: ( table: TableLeads, data: any, options: any): JSX.Element => (
                      <>{data.identifier}</>
                    )
                  },
                  title: {type: "varchar", title: this.translate("Title"), readonly: true,
                    cellRenderer: ( table: TableLeads, data: any, options: any): JSX.Element => (
                      <>{data.title}</>
                    )
                  },
                  company_name: {type: "varchar", title: this.translate("Company"), readonly: true,
                    cellRenderer: ( table: TableLeads, data: any, options: any): JSX.Element => (
                      <>{data.CUSTOMER.name}</>
                    )
                  },
                  status: {type: "interger", title: this.translate("Status"), readonly: true},
                },
                inputs: {
                  identifier: {type: "varchar", title: this.translate("Identifier"), readonly: true},
                  title: {type: "varchar", title: this.translate("Title"), readonly: true},
                  company_name: {type: "varchar", title: this.translate("Company"),readonly: true},
                  status: {type: "interger", title: this.translate("Status"), readonly: true,
                    enumValues: {
                      0: this.translate('No interaction yet'),
                      1: this.translate('Contacted'),
                      2: this.translate('In Progress'),
                      3: this.translate('Closed'),
                      20: this.translate('Converted to deal'),
                    }},
                }
              }}
            >
            </HubletoTable>
          </>
        </ModalSimple>
      : null}
    </>;
  }
}