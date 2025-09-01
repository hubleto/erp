import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import PipelineSelector from '@hubleto/apps/Pipeline/Components/PipelineSelector';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TableActivities from '@hubleto/apps/Worksheets/Components/TableActivities';
import FormInput from '@hubleto/react-ui/core/FormInput';
import request from '@hubleto/react-ui/core/Request';

export interface FormProjectProps extends HubletoFormProps { }
export interface FormProjectState extends HubletoFormState {
  statistics?: any
}

export default class FormProject<P, S> extends HubletoForm<FormProjectProps, FormProjectState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Projects/Models/Team',
  }

  props: FormProjectProps;
  state: FormProjectState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects::Components\\FormProject';

  constructor(props: FormProjectProps) {
    super(props);
  }

  getStateFromProps(props: FormProjectProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Project')}</b> },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
        { uid: 'worksheet', title: this.translate('Worksheet') },
        { uid: 'statistics', title: this.translate('Statistics') },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ]
    }
  }

  getRecordFormUrl(): string {
    return 'projects/' + this.state.record.id;
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'opacity-85 bg-slate-100' : '';
  }

  onTabChange() {
    const tabUid = this.state.activeTabUid;
    switch (tabUid) {
      case 'statistics':
        request.post(
          'projects/api/get-statistics',
          { idProject: this.state.record.id },
          {},
          (data: any) => {
            this.setState({statistics: data});
          }
        )
      break;
    }
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Project</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  renderTopMenu(): JSX.Element {
    const R = this.state.record;
    return <>
      {super.renderTopMenu()}
      {this.state.id <= 0 ? null : <>
        <PipelineSelector
          idPipeline={R.id_pipeline}
          idPipelineStep={R.id_pipeline_step}
          onPipelineChange={(idPipeline: number, idPipelineStep: number) => {
            this.updateRecord({id_pipeline: idPipeline, id_pipeline_step: idPipelineStep});
          }}
          onPipelineStepChange={(idPipelineStep: number, step: any) => {
            this.updateRecord({id_pipeline_step: idPipelineStep});
          }}
        ></PipelineSelector>
        {this.inputWrapper('is_closed', {readonly: R.is_archived})}
      </>}
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2 flex-col md:flex-row'>
            <div className='flex-1 border-r border-gray-100'>
              <FormInput title={"Order"}>
                {R.ORDERS ? R.ORDERS.map((item, key) => {
                  return (item.ORDER ? <a
                    key={key}
                    className='badge'
                    href={globalThis.main.config.projectUrl + '/orders/' + item.ORDER.id}
                    target='_blank'
                  >{item.ORDER.identifier}</a> : '#');
                }) : null}
              </FormInput>
              {this.inputWrapper('identifier')}
              {this.inputWrapper('title')}
              {this.inputWrapper('description')}
              {this.inputWrapper('id_main_developer')}
              {this.inputWrapper('id_account_manager')}
              {this.inputWrapper('priority')}
              {this.inputWrapper('date_start')}
              {this.inputWrapper('date_deadline')}
              {this.inputWrapper('budget')}
              {/* {this.inputWrapper('is_closed')} */}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('id_customer')}
              {this.inputWrapper('id_contact')}
              {this.inputWrapper('color')}
              {this.inputWrapper('online_documentation_folder')}
              {this.inputWrapper('notes')}
              {/* {this.inputWrapper('id_deal')} */}
            </div>
          </div>
        </>;
      break;
      case 'tasks':
        try {
          return <>
            {this.state.id < 0 ?
                <div className="badge badge-info">First create the project, then you will be prompted to add tasks.</div>
              :
                <TableTasks
                  tag={"table_project_task"}
                  parentForm={this}
                  uid={this.props.uid + "_table_project_task"}
                  junctionTitle='Project'
                  junctionModel='Hubleto/App/Community/Projects/Models/ProjectTask'
                  junctionSourceColumn='id_project'
                  junctionSourceRecordId={R.id}
                  junctionDestinationColumn='id_task'
                />
            }
          </>;
        } catch (ex) {
          return <div className="alert alert-error">Failed to display tasks. Check if you have 'Tasks' app installed.</div>
        }
      break;

      case 'worksheet':
        return <TableActivities
          uid={this.props.uid + "_table_activities"}
          tag="ProjectActivities"
          parentForm={this}
          idProject={R.id}
        />;
      break;

      case 'statistics':
        if (this.state.statistics) {
          console.log('stats', this.state.statistics.workedByMonth);
          return <div className='flex gap-2'>
            <div className='card'>
              <div className='card-header'>Worked hours by month</div>
              <div className='card-body'>
                <table className='table-default dense'>
                  <tbody>
                    {this.state.statistics.workedByMonth.map((item, key) => {
                      return <tr key={key}>
                        <td>{item.year}-{item.month}</td>
                        <td>{item.worked_hours} hours</td>
                      </tr>;
                    })}
                  </tbody>
                </table>
              </div>
            </div>

            <div className='card'>
              <div className='card-header'>Chargeable hours by month</div>
              <div className='card-body'>
                <table className='table-default dense'>
                  <tbody>
                    {this.state.statistics.chargeableByMonth.map((item, key) => {
                      return <tr key={key}>
                        <td>{item.year}-{item.month}</td>
                        <td>{item.worked_hours} hours</td>
                      </tr>;
                    })}
                  </tbody>
                </table>
              </div>
            </div>
          </div>;
        }
      break;

      default:
        super.renderTab(tabUid);
      break;
    }
  }

}
