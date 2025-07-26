import React, { Component, createRef, RefObject } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormDashboardProps extends HubletoFormProps {}
interface FormDashboardState extends HubletoFormState {}

export default class FormDashboard<P, S> extends HubletoForm<FormDashboardProps,FormDashboardState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Dashboards/Models/Dashboard',
  };

  props: FormDashboardProps;
  state: FormDashboardState;

  translationContext: string = 'HubletoApp\\Community\\Dashboards\\Loader::Components\\FormDashboard';

  constructor(props: FormDashboardProps) {
    super(props);
  }

  getStateFromProps(props: FormDashboardProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.title ?? ''}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <>
      <div className='card'>
        <div className='card-body'>
          {this.inputWrapper("id_owner")}
          {this.inputWrapper("title")}
          {this.inputWrapper("slug")}
          {this.inputWrapper("color")}
          {this.inputWrapper("is_default")}
        </div>
      </div>
      {this.divider(this.translate('Panels'))}
      {this.state.id < 0 ?
        <div className="badge badge-info">First create the dashboard, then you will be prompted to add panels.</div>
      :
        <div className='mt-2'>
          <Table
            uid='dashboard_panels'
            model='HubletoApp/Community/Dashboards/Models/Panel'
            customEndpointParams={{idDashboard: this.state.id}}
          ></Table>
        </div>
      }
    </>
  }
}
