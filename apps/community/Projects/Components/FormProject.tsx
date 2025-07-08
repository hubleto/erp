import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/src/core/Components/HubletoForm';
import PipelineSelector from '../../Pipeline/Components/PipelineSelector';

interface FormProjectProps extends HubletoFormProps { }
interface FormProjectState extends HubletoFormState { }

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

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.title ?? ''}</h2>
      <small>Project</small>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <>
      <div className='w-full flex gap-2'>
        <div className='flex-1 border-r border-gray-100'>
          {this.inputWrapper('id_deal')}
          {this.inputWrapper('title')}
          {this.inputWrapper('identifier')}
          {this.inputWrapper('description')}
          {this.inputWrapper('id_main_developer')}
          {this.inputWrapper('id_account_manager')}
        </div>
        <div className='flex-1'>
          {this.inputWrapper('id_customer')}
          {this.inputWrapper('id_contact')}
          {this.inputWrapper('color')}
          {this.inputWrapper('online_documentation_folder')}
          {this.inputWrapper('notes')}
        </div>
      </div>
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
    </>;
  }

}
