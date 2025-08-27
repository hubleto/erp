import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import PipelineSelector from '@hubleto/apps/Pipeline/Components/PipelineSelector';
import TableActivities from '@hubleto/apps/Worksheets/Components/TableActivities';

interface FormTaskProps extends HubletoFormProps { }
interface FormTaskState extends HubletoFormState { }

export default class FormTask<P, S> extends HubletoForm<FormTaskProps, FormTaskState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Tasks/Models/Task',
  }

  props: FormTaskProps;
  state: FormTaskState;

  translationContext: string = 'HubletoApp\\Community\\Tasks::Components\\FormTask';

  constructor(props: FormTaskProps) {
    super(props);
  }

  getStateFromProps(props: FormTaskProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Task')}</b> },
        { uid: 'worksheet', title: this.translate('Worksheet') },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ]
    }
  }

  getRecordFormUrl(): string {
    return 'tasks/' + this.state.record.id;
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'opacity-85 bg-slate-100' : '';
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Task</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  renderTopMenu() {
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
    </>
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        return <>
          <div className='w-full flex gap-2 flex-col md:flex-row'>
            <div className='flex-1 border-r border-gray-100'>
              {this.inputWrapper('identifier')}
              {this.inputWrapper('title')}
              {this.inputWrapper('description')}
              {this.inputWrapper('id_developer')}
              {this.inputWrapper('id_tester')}
              {this.inputWrapper('shared_folder')}
              {this.inputWrapper('hours_estimation')}
              {this.inputWrapper('is_closed')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('priority')}
              {this.inputWrapper('duration_days')}
              {this.inputWrapper('date_start')}
              {this.inputWrapper('date_deadline')}
              {this.inputWrapper('is_milestone')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('date_created')}
            </div>
          </div>
        </>;
      break;

      case 'worksheet':
        return <TableActivities
          uid={this.props.uid + "_table_activities"}
          tag="TaskActivities"
          parentForm={this}
          idTask={R.id}
        />;
      break;

      default:
        super.renderTab(tab);
      break;
    }
  }

}
