import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/src/core/Components/HubletoForm';
import PipelineSelector from '@hubleto/apps/community/Pipeline/Components/PipelineSelector';
import TableActivities from '@hubleto/apps/community/Worksheets/Components/TableActivities';

interface FormTaskProps extends HubletoFormProps { }
interface FormTaskState extends HubletoFormState { }

export default class FormTask<P, S> extends HubletoForm<FormTaskProps, FormTaskState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Tasks/Models/Team',
    tabs: {
      'default': { title: 'Task' },
      'worksheet': { title: 'Worksheet' },
    }
  }

  props: FormTaskProps;
  state: FormTaskState;

  translationContext: string = 'HubletoApp\\Community\\Tasks::Components\\FormTask';

  constructor(props: FormTaskProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{(this.state.record.identifier ?? '') + ' - ' + (this.state.record.title ?? '')}</h2>
      <small>Task</small>
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        return <>
          <div className='w-full flex gap-2'>
            <div className='flex-1 border-r border-gray-100'>
              {this.inputWrapper('identifier')}
              {this.inputWrapper('title')}
              {this.inputWrapper('description')}
              {this.inputWrapper('id_developer')}
              {this.inputWrapper('id_tester')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('manhours_estimation')}
              {this.inputWrapper('is_closed')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('date_created')}
              {this.inputWrapper('external_model')}
              {this.inputWrapper('external_id')}
            </div>
          </div>
          {this.state.id <= 0 ? null :
            <PipelineSelector
              idPipeline={R.id_pipeline}
              idPipelineStep={R.id_pipeline_step}
              onPipelineChange={(idPipeline: number, idPipelineStep: number) => {
                this.updateRecord({id_pipeline: idPipeline, id_pipeline_step: idPipelineStep});
              }}
              onPipelineStepChange={(idPipelineStep: number) => {
                this.updateRecord({id_pipeline_step: idPipelineStep});
              }}
            ></PipelineSelector>
          }
        </>
      break;
      case 'worksheet':
        return <>
          {this.state.id < 0 ?
              <div className="badge badge-info">First create the campaign, then you will be prompted to create leads.</div>
            :
              <TableActivities
                uid={this.props.uid + "_table_activities"}
                tag="TaskActivities"
                parentForm={this}
                idTask={R.id}
              />
          }
        </>;
      break;
    }
  }

}
