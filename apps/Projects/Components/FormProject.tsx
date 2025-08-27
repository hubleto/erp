import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import PipelineSelector from '@hubleto/apps/Pipeline/Components/PipelineSelector';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import FormInput from '@hubleto/react-ui/core/FormInput';

export interface FormProjectProps extends HubletoFormProps { }
export interface FormProjectState extends HubletoFormState { }

export default class FormProject<P, S> extends HubletoForm<FormProjectProps, FormProjectState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Projects/Models/Team',
  }

  props: FormProjectProps;
  state: FormProjectState;

  translationContext: string = 'HubletoApp\\Community\\Projects::Components\\FormProject';

  constructor(props: FormProjectProps) {
    super(props);
  }

  getStateFromProps(props: FormProjectProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Project')}</b> },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
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

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
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
                  junctionModel='HubletoApp/Community/Projects/Models/ProjectTask'
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

      default:
        super.renderTab(tab);
      break;
    }
  }

}
