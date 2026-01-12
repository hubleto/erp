import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormActivity from './FormActivity';

interface TableActivitiesProps extends HubletoTableProps {
  idTask?: number,
  idProject?: number,
}

interface TableActivitiesState extends HubletoTableState {
}

export default class TableActivities extends HubletoTable<TableActivitiesProps, TableActivitiesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Worksheets/Models/Activity',
  }

  props: TableActivitiesProps;
  state: TableActivitiesState;

  translationContext: string = 'Hubleto\\App\\Community\\Worksheets\\Loader';
  translationContextInner: string = 'Components\\TableActivities';

  constructor(props: TableActivitiesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableActivitiesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'centered small theme-secondary';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idTask: this.props.idTask,
      idProject: this.props.idProject,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/worksheets/' + (id > 0 ? id : 'add'));
  }

  renderFooter(): JSX.Element {
    let workedTotal = 0;
    for (let i in this.state.data?.data) {
      const row = this.state.data?.data[i];
      if (row['total_worked_hours']) workedTotal += parseFloat(row['total_worked_hours']);
      else workedTotal += parseFloat(row['worked_hours']);
    }

    return <>
      <div className="font-bold">
        {this.translate('Worked total')}: {globalThis.hubleto.numberFormat(workedTotal, 2, ",", " ")} {this.translate('hours')}<br/>
      </div>
    </>
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idTask = this.props.idTask;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_task: this.props.idTask };
    return <FormActivity {...formProps}/>;
  }
}