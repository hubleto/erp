import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/src/core/Components/HubletoTable';
import FormLead, { FormLeadProps } from './FormLead';

export interface TableLeadsProps extends HubletoTableProps {
  idCustomer?: number,
}

export interface TableLeadsState extends HubletoTableState {
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
    this.state = this.getStateFromProps(props);
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
    return <FormLead {...formProps}/>;
  }
}