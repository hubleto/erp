import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import PipelineSelector from '@hubleto/apps/Pipeline/Components/PipelineSelector';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';

export interface FormProjectProps extends HubletoFormProps { }
export interface FormProjectState extends HubletoFormState { }

export default class FormProject<P, S> extends HubletoForm<FormProjectProps, FormProjectState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Projects/Models/Team',
    tabs: {
      'default': { title: 'Project' },
      'tasks': { title: 'Tasks' },
      'statistics': { title: 'Statistics' },
    }
  }

  props: FormProjectProps;
  state: FormProjectState;

  translationContext: string = 'HubletoApp\\Community\\Projects::Components\\FormProject';

  constructor(props: FormProjectProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{(this.state.record.identifier ?? '') + ' ' + (this.state.record.title ?? '')}</h2>
      <small>Project</small>
    </>;
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
              {this.inputWrapper('id_main_developer')}
              {this.inputWrapper('id_account_manager')}
              {this.inputWrapper('priority')}
              {this.inputWrapper('date_start')}
              {this.inputWrapper('date_deadline')}
              {this.inputWrapper('budget')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('id_customer')}
              {this.inputWrapper('id_contact')}
              {this.inputWrapper('color')}
              {this.inputWrapper('online_documentation_folder')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('id_deal')}
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
        </>;
      break;
      case 'tasks':
        try {
          return <>
            {this.state.id < 0 ?
                <div className="badge badge-info">First create the project, then you will be prompted to add tasks.</div>
              :
                <TableTasks
                  uid={this.props.uid + "_table_tasks"}
                  tag="ProjectTasks"
                  parentForm={this}
                  externalModel='HubletoApp\Community\Projects\Models\Project'
                  externalId={R.id}
                  selectionMode='multiple'
                />
            }
          </>;
        } catch (ex) {
          return <div className="alert alert-error">Failed to display tasks. Check if you have 'Tasks' app installed.</div>
        }
      break;
    }
  }

}
